<?php

namespace Foxen\LaravelModelActivityLog\Providers;

use Illuminate\Support\ServiceProvider;

class ActivityLogServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes(
            [
                __DIR__.'/../../config/foxen_activitylog.php' => config_path(
                    'foxen_activitylog.php'
                ),
            ],
            'config'
        );

        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../config/foxen_activitylog.php',
            'foxen_activitylog'
        );
    }
}
