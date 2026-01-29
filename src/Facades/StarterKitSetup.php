<?php

namespace Onelegstudios\StarterKitSetup\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Onelegstudios\StarterKitSetup\StarterKitSetup
 */
class StarterKitSetup extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Onelegstudios\StarterKitSetup\StarterKitSetup::class;
    }
}
