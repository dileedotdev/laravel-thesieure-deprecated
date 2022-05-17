<?php

namespace Dinhdjj\Thesieure;

use Dinhdjj\Thesieure\Commands\ThesieureCommand;
use Dinhdjj\Thesieure\Exceptions\InvalidThesieureConfigException;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ThesieureServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('thesieure')
            ->hasConfigFile()
            // ->hasViews()
            // ->hasMigration('create_thesieure_table')
            // ->hasCommand(ThesieureCommand::class)
            ;
    }

    public function packageRegistered(): void
    {
        $this->app->singleton('thesieure', function () {
            if (!config('thesieure.domain') || !config('thesieure.partner_id') || !config('thesieure.partner_key')) {
                throw new InvalidThesieureConfigException();
            }

            return new Thesieure(config('thesieure.domain'), config('thesieure.partner_id'), config('thesieure.partner_key'));
        });
    }

    public function packageBooted(): void
    {
        $this->loadRoutesFrom(__DIR__.'/api.php');
    }
}
