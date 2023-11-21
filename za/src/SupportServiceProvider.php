<?php

namespace Za\Support;

use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;

class SupportServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerRequestMacros();
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
        return ['support'];
    }

    protected function registerRequestMacros()
    {
        Request::macro('jsonInput', function ($key = null, $default = null) {
            $val = $this->input($key, $default);

            return is_array($val) ? $val : json_decode($val, true);
        });

        Request::macro('jsonInputOnly', function ($keys) {
            return array_map(function ($data) {
                return is_array($data) ? $data : json_decode($data, true);
            }, $this->only($keys));
        });

        Request::macro('jsonInputExcept', function ($keys) {
            return array_map(function ($data) {
                return is_array($data) ? $data : json_decode($data, true);
            }, $this->except($keys));
        });
    }
}
