<?php

namespace Za\Support\OTP;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class OTPServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishMigrations();
            $this->commands([
                ControllerCommand::class,
            ]);
        }

        Route::macro('otpPasswordReset', function () {
            $this->post('otp/password', 'OTP\PasswordResetController@request')->name('otp.password.request');
            $this->post('otp/password/reset', 'OTP\PasswordResetController@reset')->name('otp.password.reset');
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
        return ['otp'];
    }

    protected function publishMigrations()
    {
        if (class_exists('CreateOtpPasswordResetTable')) {
            return;
        }

        $timestamp = date('Y_m_d_His', time());

        $stub = __DIR__.'/../../database/migrations/create_otp_password_reset_table.php';

        $target = $this->app->databasePath('/migrations/'.$timestamp.'_create_otp_password_reset_table.php');

        $this->publishes([$stub => $target], 'za.otp.migrations');
    }
}
