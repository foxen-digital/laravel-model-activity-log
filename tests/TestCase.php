<?php

namespace Foxen\LaravelModelActivityLog\Tests;

use Foxen\LaravelModelActivityLog\Providers\ActivityLogServiceProvider;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    use RefreshDatabase;

    protected function getPackageProviders($app)
    {
        return [ActivityLogServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app)
    {
        $migration = include __DIR__ .
            "/migrations/2025_07_07_000001_create_posts_table.php";
        $migration->up();

        $migration = include __DIR__ .
            "/migrations/2025_07_07_000002_create_users_table.php";
        $migration->up();
    }
}
