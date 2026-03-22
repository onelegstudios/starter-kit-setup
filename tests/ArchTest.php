<?php

use Illuminate\Console\Command;
use Spatie\LaravelPackageTools\PackageServiceProvider;

arch('it will not use debugging functions')
    ->expect(['dd', 'dump', 'ray'])
    ->each->not->toBeUsed();

arch('commands extend the console command base class')
    ->expect('Onelegstudios\\StarterKitSetup\\Commands')
    ->toBeClasses()
    ->toExtend(Command::class)
    ->toHaveSuffix('Command');

arch('service provider extends package service provider')
    ->expect('Onelegstudios\\StarterKitSetup\\StarterKitSetupServiceProvider')
    ->toExtend(PackageServiceProvider::class);
