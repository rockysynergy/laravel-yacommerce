<?php
namespace Tests;

use Orchestra\Testbench\TestCase;
abstract class DbTestCase extends TestCase
{

    /**
     * Setup the test environment.
     */
    protected function setUp():void
    {
        parent::setUp();
        $this->loadMigrationsFrom(__DIR__ . '/../migrations');
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'fx_nhqs_testing'),
            'username' => env('DB_USERNAME', 'nhqs_xcx'),
            'password' => env('DB_PASSWORD', 'NHQSdb@2'),
        ]);
    }
}
