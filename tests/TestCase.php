<?php

namespace Dinhdjj\Thesieure\Tests;

use Dinhdjj\Thesieure\ThesieureServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Dinhdjj\\Thesieure\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            ThesieureServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app): void
    {
        config()->set('database.default', 'testing');
        config()->set('thesieure.partner_id', '6797019371');
        config()->set('thesieure.partner_key', 'ae76b17456215a4d861d7ddae5347da3');

        /*
        $migration = include __DIR__.'/../database/migrations/create_thesieure_table.php.stub';
        $migration->up();
        */
    }
}
