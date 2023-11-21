<?php

namespace Za\Support\ZaImage;

use Illuminate\Support\ServiceProvider;

class ZaImageServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../../config/za_image.php' => config_path('za_image.php'),
            ], 'za_image');
        }

        $this->commands([]);

        $this->app->singleton(ZaImage::class, function ($app) {
            return new ZaImage();
        });
    }
}
