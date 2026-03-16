<?php

namespace Onelegstudios\StarterKitSetup;

use Onelegstudios\StarterKitSetup\Commands\AddMailpitCommand;
use Onelegstudios\StarterKitSetup\Commands\UsingBuiltInServerCommand;
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
            ->hasCommand(UsingBuiltInServerCommand::class)
            ->hasCommand(AddMailpitCommand::class);
    }
}
