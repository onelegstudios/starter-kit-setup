<?php

namespace Onelegstudios\StarterKitSetup\Commands;

use Illuminate\Console\Command;
use Onelegstudios\StarterKitSetup\Concerns\WritesFilesAtomically;

class SetupCommand extends Command
{
    use WritesFilesAtomically;

    protected $signature = 'starter-kit-setup:setup';

    protected $description = 'Run all starter-kit-setup commands.';

    public function handle(): int
    {
        $this->info('Running starter-kit-setup commands...');

        $configPath = config_path('solo.php');
        $originalContent = $this->snapshotConfigContent($configPath);

        $usingBuiltInServerExitCode = $this->call('starter-kit-setup:using-built-in-server');

        if ($usingBuiltInServerExitCode !== self::SUCCESS) {
            $this->error('The using-built-in-server command failed.');

            return self::FAILURE;
        }

        $addMailpitExitCode = $this->call('starter-kit-setup:add-mailpit');

        if ($addMailpitExitCode !== self::SUCCESS) {
            if (! $this->restoreConfigContent($configPath, $originalContent)) {
                $this->error('Rollback failed after add-mailpit command failed.');

                return self::FAILURE;
            }

            $this->error('The add-mailpit command failed.');

            return self::FAILURE;
        }

        $this->info('All starter-kit-setup commands completed successfully.');

        return self::SUCCESS;
    }

    private function snapshotConfigContent(string $configPath): ?string
    {
        if (! file_exists($configPath) || ! is_file($configPath) || ! is_readable($configPath)) {
            return null;
        }

        $content = file_get_contents($configPath);

        if ($content === false) {
            return null;
        }

        return $content;
    }

    private function restoreConfigContent(string $configPath, ?string $originalContent): bool
    {
        if ($originalContent === null) {
            return true;
        }

        return $this->replaceFileContent($configPath, $originalContent);
    }
}
