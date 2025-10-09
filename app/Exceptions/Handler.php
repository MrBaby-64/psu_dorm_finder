<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register exception callbacks
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            // Log errors in production for debugging
            if (app()->environment('production')) {
                Log::error('Production Exception', [
                    'exception' => get_class($e),
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'url' => request()->fullUrl(),
                    'method' => request()->method(),
                    'user_id' => auth()->id(),
                    'ip' => request()->ip(),
                    'user_agent' => request()->header('User-Agent'),
                ]);
            }
        });
    }

    /**
     * Render exception as HTTP response
     */
    public function render($request, Throwable $e)
    {
        // Handle timeout errors (database connection issues)
        if ($e instanceof \Symfony\Component\ErrorHandler\Error\FatalError &&
            str_contains($e->getMessage(), 'Maximum execution time')) {

            if (app()->environment('local')) {
                return response()->view('errors.database-timeout', [
                    'message' => 'Database connection timeout. Please make sure MySQL is running in XAMPP Control Panel.'
                ], 500);
            }

            return response()->view('errors.503', [
                'message' => 'Service temporarily unavailable. Please try again.'
            ], 503);
        }

        // Handle 404 not found errors
        if ($e instanceof NotFoundHttpException) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Resource not found'], 404);
            }
            return response()->view('errors.404', [], 404);
        }

        // Handle other HTTP exceptions
        if ($e instanceof HttpException) {
            $statusCode = $e->getStatusCode();

            if ($request->expectsJson()) {
                return response()->json([
                    'error' => $this->getHttpErrorMessage($statusCode),
                    'code' => $statusCode
                ], $statusCode);
            }

            // Try to render custom error page
            if (view()->exists("errors.{$statusCode}")) {
                return response()->view("errors.{$statusCode}", [
                    'message' => $this->getHttpErrorMessage($statusCode)
                ], $statusCode);
            }
        }

        // Handle database connection errors
        if ($this->isDatabaseConnectionError($e)) {
            Log::error('Database Connection Error', [
                'exception' => get_class($e),
                'message' => $e->getMessage()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Service temporarily unavailable',
                    'code' => 503
                ], 503);
            }

            return response()->view('errors.503', [
                'message' => 'Database connection failed. Our team has been notified.'
            ], 503);
        }

        // In production, render user-friendly 500 error
        if (app()->environment('production') && !$this->shouldntReport($e)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'An unexpected error occurred. Please try again.',
                    'code' => 500
                ], 500);
            }

            return response()->view('errors.500', [
                'message' => 'Something went wrong. Our team has been notified and is working to fix this.'
            ], 500);
        }

        return parent::render($request, $e);
    }

    /**
     * Check if error is database connection issue
     */
    private function isDatabaseConnectionError(Throwable $e): bool
    {
        $message = strtolower($e->getMessage());
        return str_contains($message, 'connection refused') ||
               str_contains($message, 'connection failed') ||
               str_contains($message, 'database') && str_contains($message, 'connection') ||
               str_contains($message, 'could not find driver') ||
               str_contains($message, 'sqlstate');
    }

    /**
     * Get error message for status code
     */
    private function getHttpErrorMessage(int $statusCode): string
    {
        return match($statusCode) {
            400 => 'Bad request. Please check your input.',
            401 => 'Authentication required.',
            403 => 'Access denied. You don\'t have permission to access this resource.',
            404 => 'The page you\'re looking for doesn\'t exist.',
            405 => 'Method not allowed.',
            422 => 'The given data was invalid.',
            429 => 'Too many requests. Please slow down.',
            500 => 'Internal server error. Please try again later.',
            502 => 'Bad gateway. The server is temporarily unavailable.',
            503 => 'Service unavailable. Please try again in a moment.',
            default => 'An error occurred.'
        };
    }
}