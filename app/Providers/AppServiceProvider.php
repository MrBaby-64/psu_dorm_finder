<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Set application timezone
        date_default_timezone_set('Asia/Manila');

        // Force HTTPS in production
        if (app()->environment('production')) {
            URL::forceScheme('https');

            if ($appUrl = config('app.url')) {
                URL::forceRootUrl($appUrl);
            }
        }

        // Add cross-database LIKE macro for case-insensitive search
        // Works with both MySQL and PostgreSQL
        Builder::macro('whereLike', function ($column, $value) {
            $driver = DB::connection()->getDriverName();

            if ($driver === 'pgsql') {
                // PostgreSQL: use ILIKE for case-insensitive search
                return $this->where($column, 'ILIKE', $value);
            }

            // MySQL, SQLite, etc: use LIKE (case-insensitive by default in MySQL)
            return $this->where($column, 'LIKE', $value);
        });

        Builder::macro('orWhereLike', function ($column, $value) {
            $driver = DB::connection()->getDriverName();

            if ($driver === 'pgsql') {
                return $this->orWhere($column, 'ILIKE', $value);
            }

            return $this->orWhere($column, 'LIKE', $value);
        });
    }
}
