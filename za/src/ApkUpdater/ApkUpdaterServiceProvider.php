<?php

namespace Za\Support\ApkUpdater;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class ApkUpdaterServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                ControllerCommand::class,
            ]);
        }

        Route::macro('apkUpdater', function () {
            $this->get('apk-updater/check', 'ZaApkUpdaterController@check')->name('za.apk_update_check');
        });
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['apk_updater'];
    }
}
