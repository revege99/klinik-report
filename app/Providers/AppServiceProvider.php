<?php

namespace App\Providers;

use App\Models\ClinicProfile;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            static $clinicProfileLoaded = false;
            static $clinicProfile = null;

            if (! $clinicProfileLoaded) {
                $clinicProfileLoaded = true;

                if (Schema::hasTable('clinic_profiles')) {
                    $clinicProfile = ClinicProfile::query()->first();
                }
            }

            $view->with('sharedClinicProfile', $clinicProfile);
        });
    }
}
