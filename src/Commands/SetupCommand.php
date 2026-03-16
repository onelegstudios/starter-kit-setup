<?php

namespace Onelegstudios\StarterKitSetup\Commands;

use Illuminate\Console\Command;

class SetupCommand extends Command
{
    protected $signature = 'starter-kit-setup:setup';

    protected $description = 'Run all starter-kit-setup commands.';

    public function handle(): int
    {
        $this->info('Running starter-kit-setup commands...');

        $usingBuiltInServerExitCode = $this->call('starter-kit-setup:using-built-in-server');

        if ($usingBuiltInServerExitCode !== self::SUCCESS) {
            $this->error('The using-built-in-server command failed.');

            return self::FAILURE;
        }

        $addMailpitExitCode = $this->call('starter-kit-setup:add-mailpit');

        if ($addMailpitExitCode !== self::SUCCESS) {
            $this->error('The add-mailpit command failed.');

            return self::FAILURE;
        }

        $this->info('All starter-kit-setup commands completed successfully.');

        return self::SUCCESS;
    }
}
