<?php

namespace App\Captcha;

use Illuminate\Support\ServiceProvider;

class CaptchaServiceProvider extends ServiceProvider
{
    /**
     * Boot the service provider.
     *
     * @return null
     */
    public function boot()
    {
        // Publish configuration files
        $this->publishes([
            __DIR__.'/config/captcha.php' => config_path('captcha.php')
        ], 'config');

        // HTTP routing
        $this->app['router']->get('captcha/{config?}', '\App\Captcha\CaptchaController@getCaptcha');

        // Validator extensions
        $this->app['validator']->extend('captcha', function($attribute, $value, $parameters)
        {
            return captcha_check($value);
        });
    }

    public function register()
    {
        // Merge configs
        $this->mergeConfigFrom(
            __DIR__.'/config/captcha.php', 'captcha'
        );

        // Bind captcha
        $this->app->bind('captcha', function($app)
        {
            return new Captcha(
                $app['Illuminate\Filesystem\Filesystem'],
                $app['Illuminate\Config\Repository'],
                $app['Intervention\Image\ImageManager'],
                $app['Illuminate\Contracts\Cache\Repository'],
                $app['Illuminate\Support\Str']
            );
        });
    }
}