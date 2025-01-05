<?php

namespace MuhammadZahid\UniversalDate;

use Illuminate\Support\ServiceProvider;

class UniversalDateServiceProvider extends ServiceProvider
{
    /**
    * Register services.
    *
    * @return void
    */
    public function register()
    {
        $this->app->singleton('universaldate', function ($app) {
            return new UniversalDate();
        });
    }

    /**
    * Bootstrap any package services.
    *
    * @return void
    */
    public function boot()
    {
        // Add any boot configurations here if needed in the future
        // Such as publishing configurations, migrations, etc.
    }

    /**
    * Get the services provided by the provider.
    *
    * @return array
    */
    public function provides()
    {
        return ['universaldate'];
    }
}

