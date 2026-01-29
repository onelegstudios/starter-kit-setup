<?php

namespace Onelegstudios\StarterKitSetup;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Onelegstudios\StarterKitSetup\Commands\StarterKitSetupCommand;

class StarterKitSetupServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('starter-kit-setup')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_starter_kit_setup_table')
            ->hasCommand(StarterKitSetupCommand::class);
    }
}
