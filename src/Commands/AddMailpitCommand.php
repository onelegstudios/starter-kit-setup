<?php

namespace Onelegstudios\StarterKitSetup\Commands;

use Illuminate\Console\Command;

class AddMailpitCommand extends Command
{
    private const SOLO_MAILPIT_LINE = "        'Milpit' => Command::from('mailpt')->lazy(),";

    private const SOLO_INSERT_ANCHOR = '        // Lazy commands do not automatically start when Solo starts.';

    protected $signature = 'starter-kit-setup:add-mailpit';

    protected $description = 'Add Milpit lazy command to soloterm/solo config file.';

    public function handle(): int
    {
        $configPath = config_path('solo.php');

        if (! file_exists($configPath)) {
            $this->error('Config file solo.php not found.');

            return self::FAILURE;
        }

        if (! is_file($configPath)) {
            $this->error('Unable to read config file solo.php.');

            return self::FAILURE;
        }

        $content = file_get_contents($configPath);

        if (str_contains($content, self::SOLO_MAILPIT_LINE)) {
            $this->info('Milpit command is already present in solo.php configuration.');

            return self::SUCCESS;
        }

        $updated = str_replace(
            self::SOLO_INSERT_ANCHOR,
            self::SOLO_MAILPIT_LINE."\n\n".self::SOLO_INSERT_ANCHOR,
            $content,
            $replacements
        );

        if ($replacements === 0) {
            $this->error('Unable to update solo.php: insertion anchor not found.');

            return self::FAILURE;
        }

        file_put_contents($configPath, $updated);

        $this->info('Successfully added Milpit command to solo.php configuration.');

        return self::SUCCESS;
    }
}
