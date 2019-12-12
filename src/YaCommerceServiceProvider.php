<?php

namespace Orq\Laravel\YaCommerce;

use Illuminate\Support\ServiceProvider;

class YaCommerceServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../configs/yac.config.php', 'yac');
        $this->mergeConfigFrom(__DIR__.'/../configs/pay.config.php', 'pay');
        $this->mergeConfigFrom(__DIR__.'/../configs/shiptracking.config.php', 'shiptracking');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../configs/yac.config.php' => config_path('yac.php'),
            __DIR__.'/../configs/pay.config.php' => config_path('pay.php'),
            __DIR__.'/../configs/shiptracking.config.php' => config_path('shiptracking.php'),
        ], 'configs');
        $this->publishes([
            __DIR__.'/../assets' => public_path('vendor/YaCommerce')
        ], 'assets');

        $this->loadMigrationsFrom(__DIR__.'/../migrations');

        $this->loadViewsFrom(__DIR__.'/../views', 'YaCommerce');
        $this->publishes([
            __DIR__.'/../views' => resource_path('views/vendor/YaCommerce'),
        ], 'views');

        $this->loadTranslationsFrom(__DIR__.'/../translations', 'YaCommerce');
        $this->publishes([
            __DIR__.'/../translations' => resource_path('lang/vendor/YaCommerce'),
        ]);
    }
}
