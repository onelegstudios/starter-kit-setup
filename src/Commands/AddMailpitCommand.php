<?php

namespace Onelegstudios\StarterKitSetup\Commands;

use Illuminate\Console\Command;
use Onelegstudios\StarterKitSetup\Concerns\InteractsWithSoloConfig;

class AddMailpitCommand extends Command
{
    use InteractsWithSoloConfig;

    private const SOLO_MAILPIT_LINE = "        'Mailpit' => Command::from('mailpit')->lazy(),";

    /** Matches the Mailpit key regardless of surrounding whitespace. */
    private const MAILPIT_PRESENT_PATTERN = '/^\h*\'Mailpit\'\h*=>/m';

    /** Matches the lazy commands anchor comment, tolerant of whitespace and wording variations. */
    private const LAZY_ANCHOR_PATTERN = '/^(\h*\/\/\h*Lazy commands do not automatically start[^\n]*)/m';

    /** Matches the full commands array block so we can append before its closing bracket. */
    private const COMMANDS_BLOCK_PATTERN = '/^(\h*\'commands\'\h*=>\h*\[\h*\R)(.*?)(^\h*],(?:\h*\R|\h*$))/ms';

    protected $signature = 'starter-kit-setup:add-mailpit';

    protected $description = 'Add Mailpit lazy command to soloterm/solo config file.';

    public function handle(): int
    {
        $content = $this->readSoloConfigContent();

        if ($content === null) {
            return self::FAILURE;
        }

        if (preg_match(self::MAILPIT_PRESENT_PATTERN, $content)) {
            $this->info('Mailpit command is already present in solo.php configuration.');

            return self::SUCCESS;
        }

        $updated = preg_replace(
            self::LAZY_ANCHOR_PATTERN,
            '$1'."\n".self::SOLO_MAILPIT_LINE,
            $content,
            1,
            $replacements
        );

        if ($updated === null) {
            $this->error('Unable to update solo.php: insertion anchor not found.');

            return self::FAILURE;
        }

        if ($replacements === 0) {
            $updated = $this->insertIntoCommandsArray($content);
        }

        if ($updated === null) {
            $this->error('Unable to update solo.php: insertion anchor not found.');

            return self::FAILURE;
        }

        if (! $this->writeSoloConfigContent($updated)) {
            return self::FAILURE;
        }

        $this->info('Successfully added Mailpit command to solo.php configuration.');

        return self::SUCCESS;
    }

    private function insertIntoCommandsArray(string $content): ?string
    {
        $updated = preg_replace_callback(
            self::COMMANDS_BLOCK_PATTERN,
            static function (array $matches): string {
                $commandsContent = $matches[2];

                if ($commandsContent !== '' && ! str_ends_with($commandsContent, "\n")) {
                    $commandsContent .= "\n";
                }

                return $matches[1].$commandsContent.self::SOLO_MAILPIT_LINE."\n".$matches[3];
            },
            $content,
            1,
            $replacements
        );

        if ($updated === null || $replacements === 0) {
            return null;
        }

        return $updated;
    }
}
