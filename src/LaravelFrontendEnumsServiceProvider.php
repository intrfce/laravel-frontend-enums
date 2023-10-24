<?php

namespace Intrfce\LaravelFrontendEnums;

use Illuminate\Support\ServiceProvider;
use Intrfce\LaravelFrontendEnums\Console\Commands\PublishEnumsCommand;

class LaravelFrontendEnumsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        /*
         * Optional methods to load your package assets
         */
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'laravel-frontend-enums');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'laravel-frontend-enums');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('laravel-frontend-enums.php'),
            ], 'config');

            // Publishing the views.
            /*$this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/laravel-frontend-enums'),
            ], 'views');*/

            // Publishing assets.
            /*$this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/laravel-frontend-enums'),
            ], 'assets');*/

            // Publishing the translation files.
            /*$this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/laravel-frontend-enums'),
            ], 'lang');*/

            // Registering package commands.
            $this->commands([
                PublishEnumsCommand::class,
            ]);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // // Automatically apply the package configuration
        // $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'laravel-frontend-enums');

        // Register the main class to use with the facade
        $this->app->singleton('publish_enums_registry', function () {
            return new Registry;
        });
    }
}
