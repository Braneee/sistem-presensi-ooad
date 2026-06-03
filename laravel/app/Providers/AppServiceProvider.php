<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(\App\Services\FlaskService::class);
        $this->app->singleton(\App\Services\AttendanceService::class);
        $this->app->singleton(\App\Services\ReportService::class);
    }

    public function boot(): void
    {
        //
    }
}
