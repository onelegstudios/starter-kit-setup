<?php

namespace Onelegstudios\StarterKitSetup\Commands;

use Illuminate\Console\Command;

class StarterKitSetupCommand extends Command
{
    public $signature = 'starter-kit-setup';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
