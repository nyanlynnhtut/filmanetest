<?php

namespace Za\Support\LanguageSwitch\Middlewares;

use Closure;

class SetApplicationLocale
{
    const LOCALE_KEY_NAME = '__lang';

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $lang = $this->getLocaleFromStorage($request);

        if ($lang && in_array($lang, config('language_switch.languages'))) {
            app()->setLocale($lang);
        }

        return $next($request);
    }

    protected function getLocaleFromStorage($request)
    {
        // If storage type is cookie, return from cookie jar
        // else return from session store
        if (config('language_switch.storage') === 'cookie') {
            return $request->cookie(static::LOCALE_KEY_NAME);
        }

        return $request->session()->get(static::LOCALE_KEY_NAME);
    }
}
