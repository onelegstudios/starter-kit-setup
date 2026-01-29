<?php

namespace Onelegstudios\StarterKitSetup;

use Onelegstudios\StarterKitSetup\Commands\StarterKitSetupCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

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
            ->hasCommand(StarterKitSetupCommand::class);
    }
}
