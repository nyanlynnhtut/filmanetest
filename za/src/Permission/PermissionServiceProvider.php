<?php

namespace Za\Support\Permission;

use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class PermissionServiceProvider extends ServiceProvider
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
                __DIR__.'/../../config/permissions.php' => config_path('permissions.php'),
            ], 'za.permission.config');
            $this->publishMigrations();
        }

        $this->commands([
            Commands\PermissionRefresh::class,
            Commands\RoleCreate::class,
        ]);

        Route::macro('permission', function ($name) {
            $this->middleware('permission:'.$name);
        });

        Blade::if('permit', function ($permission) {
            return auth()->user() && auth()->user()->permit($permission);
        });
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/permissions.php', 'za.permission.config');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['permission'];
    }

    protected function publishMigrations()
    {
        if (class_exists('CreatePermissionsTables')) {
            return;
        }

        $timestamp = date('Y_m_d_His', time());

        $stub = __DIR__.'/../../database/migrations/create_permissions_tables.php';

        $target = $this->app->databasePath('/migrations/'.$timestamp.'_create_permissions_tables.php');

        $this->publishes([$stub => $target], 'za.permission.migrations');
    }
}
