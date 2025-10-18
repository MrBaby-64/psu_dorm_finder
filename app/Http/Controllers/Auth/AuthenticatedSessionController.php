<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * AuthenticatedSessionController
 *
 * Manages user authentication including login, logout, and session handling
 * with role-based redirects to appropriate dashboards.
 */
class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login form
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Process login request and redirect to role-specific dashboard
     * Includes fresh_login parameter for back button prevention
     */
   public function store(LoginRequest $request): RedirectResponse
{
    $request->authenticate();
    $request->session()->regenerate();

    $user = auth()->user();

    // Role-based redirect with fresh login flag for security
    if ($user->role === 'landlord') {
        return redirect()->route('landlord.account', ['fresh_login' => '1']);
    } elseif ($user->role === 'admin') {
        return redirect()->route('admin.dashboard', ['fresh_login' => '1']);
    } else {
        return redirect()->route('tenant.account', ['fresh_login' => '1']);
    }
}

    /**
     * Log out the authenticated user and destroy their session
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
