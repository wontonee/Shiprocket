<?php

namespace Wontonee\Shiprocket\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;

class ShiprocketServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {


        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');

        $this->loadRoutesFrom(__DIR__ . '/../Routes/admin-routes.php');

        $this->loadRoutesFrom(__DIR__ . '/../Routes/shop-routes.php');

        $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'shiprocket');

        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'shiprocket');


        $this->publishes([
            __DIR__ . '/../public/build' => public_path('themes/shiprocket/default/build'),
        ], 'shiprocket-assets');
     

        Event::listen('bagisto.admin.layout.head.after', function ($viewRenderEventManager) {
            $viewRenderEventManager->addTemplate('shiprocket::admin.layouts.style');
        });

        Event::listen('bagisto.admin.sales.order.page_action.after', function ($viewRenderEventManager) {
            $viewRenderEventManager->addTemplate('shiprocket::admin.sales.orders.shiprocket-button-template');
        });
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerConfig();
    }

    /**
     * Register package config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/admin-menu.php',
            'menu.admin'
        );

        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/acl.php',
            'acl'
        );
    }
}
