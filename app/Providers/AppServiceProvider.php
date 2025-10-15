<?php

namespace App\Providers;

use App\Services\StudentService;
use App\Services\CourseService;
use App\Services\EnrollmentService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(StudentService::class, function ($app) {
            return new StudentService();
        });

        $this->app->singleton(CourseService::class, function ($app) {
            return new CourseService();
        });

        $this->app->singleton(EnrollmentService::class, function ($app) {
            return new EnrollmentService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
