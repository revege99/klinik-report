<?php

namespace App\Providers;

use App\Models\AppSetting;
use App\Models\ClinicProfile;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
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
            static $appSettingsLoaded = false;
            static $appSettings = [];

            if (! $clinicProfileLoaded) {
                $clinicProfileLoaded = true;

                if (Schema::hasTable('clinic_profiles')) {
                    $user = Auth::user();
                    $requestedClinic = strtolower(trim((string) request()->query('clinic_id', '')));

                    if ($user?->isMaster()) {
                        if ($requestedClinic === 'all') {
                            $clinicProfile = null;
                        } else {
                            $requestedClinicId = (int) $requestedClinic;
                            $clinicProfile = $requestedClinicId > 0
                                ? ClinicProfile::query()->active()->find($requestedClinicId)
                                : null;
                        }
                    } elseif ($user?->clinic_profile_id) {
                        $clinicProfile = ClinicProfile::query()
                            ->active()
                            ->find($user->clinic_profile_id);
                    }

                    if (! $clinicProfile && ! $user?->isMaster() && $requestedClinic !== 'all') {
                        $clinicProfile = ClinicProfile::query()
                            ->active()
                            ->orderBy('id')
                            ->first();
                    }
                }
            }

            if (! $appSettingsLoaded) {
                $appSettingsLoaded = true;

                if (Schema::hasTable('app_settings')) {
                    $appSettings = AppSetting::query()
                        ->orderBy('setting_group')
                        ->orderBy('setting_key')
                        ->pluck('setting_value', 'setting_key')
                        ->toArray();
                }
            }

            $view->with('sharedClinicProfile', $clinicProfile);
            $view->with('sharedAppSettings', $appSettings);
        });
    }
}
