<?php

namespace Dinhdjj\Thesieure;

use Dinhdjj\Thesieure\Commands\ThesieureCommand;
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
            // ->hasConfigFile()
            // ->hasViews()
            // ->hasMigration('create_thesieure_table')
            // ->hasCommand(ThesieureCommand::class)
            ;
    }

    public function packageRegistered()
    {
        $this->app->singleton('thesieure', function () {
            return new Thesieure();
        });
    }
}
