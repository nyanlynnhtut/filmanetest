<?php

namespace Za\Support\LanguageSwitch;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class LanguageSwitchServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../../config/language_switch.php' => config_path('language_switch.php'),
            ], 'za.language.switch.config');
            $this->commands([
                ControllerCommand::class,
            ]);
        }

        Route::macro('languageSwitch', function () {
            $this->get('language-switch/{locale}', 'LanguageSwitch\LanguageSwitchController')->name('language.switch');
        });
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/language_switch.php', 'za.language.switch.config');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['language.switch'];
    }
}
