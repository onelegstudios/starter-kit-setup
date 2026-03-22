<?php
namespace Onelegstudios\StarterKitSetup\Commands;

use Illuminate\Console\Command;

class AddMailpitCommand extends Command
{
    private const SOLO_MAILPIT_LINE = "        'Mailpit' => Command::from('mailpit')->lazy(),";

    /** Matches the Mailpit key regardless of surrounding whitespace. */
    private const MAILPIT_PRESENT_PATTERN = '/^\h*\'Mailpit\'\h*=>/m';

    /** Matches the lazy commands anchor comment, tolerant of whitespace and wording variations. */
    private const LAZY_ANCHOR_PATTERN = '/^(\h*\/\/\h*Lazy commands do not automatically start[^\n]*)/m';

    protected $signature = 'starter-kit-setup:add-mailpit';

    protected $description = 'Add Mailpit lazy command to soloterm/solo config file.';

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

        if (! is_readable($configPath)) {
            $this->error('Config file solo.php could not be read.');

            return self::FAILURE;
        }

        $content = file_get_contents($configPath);

        if ($content === false) {
            $this->error('Config file solo.php could not be read.');

            return self::FAILURE;
        }

        if (preg_match(self::MAILPIT_PRESENT_PATTERN, $content)) {
            $this->info('Mailpit command is already present in solo.php configuration.');

            return self::SUCCESS;
        }

        $updated = preg_replace(
            self::LAZY_ANCHOR_PATTERN,
            '$1' . "\n" . self::SOLO_MAILPIT_LINE,
            $content,
            1,
            $replacements
        );

        if ($replacements === 0) {
            $this->error('Unable to update solo.php: insertion anchor not found.');

            return self::FAILURE;
        }

        try {
            $bytesWritten = file_put_contents($configPath, $updated);
        } catch (\ErrorException) {
            $this->error('Unable to update solo.php: write failed.');

            return self::FAILURE;
        }

        if ($bytesWritten === false) {
            $this->error('Unable to update solo.php: write failed.');

            return self::FAILURE;
        }

        $this->info('Successfully added Mailpit command to solo.php configuration.');

        return self::SUCCESS;
    }
}
