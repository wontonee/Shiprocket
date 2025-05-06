<?php

namespace Wontonee\ShipRocket\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;

class ShipRocketServiceProvider extends ServiceProvider
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

        Event::listen('bagisto.admin.layout.head', function($viewRenderEventManager) {
            $viewRenderEventManager->addTemplate('shiprocket::admin.layouts.style');
        });

        // Debug: Log all menu items to find the correct parent key
        \Event::listen('menu.build.after', function (&$menuItems) {
            \Log::info('Bagisto Menu Items:', $menuItems);
            $menuItems[] = [
                'key'      => 'shiprocket-settings',
                'name'     => 'Shiprocket Settings',
                'route'    => 'admin.shiprocket.settings',
                'sort'     => 99,
                'icon'     => 'temp-icon',
                'parent'   => 'system', // Still trying 'system' as parent
            ];
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
            dirname(__DIR__) . '/Config/admin-menu.php', 'menu.admin'
        );

        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/acl.php', 'acl'
        );
    }
}