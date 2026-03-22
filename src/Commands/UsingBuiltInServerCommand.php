<?php

namespace Onelegstudios\StarterKitSetup\Commands;

use Illuminate\Console\Command;
use Onelegstudios\StarterKitSetup\Concerns\InteractsWithSoloConfig;

use function Laravel\Prompts\confirm;

class UsingBuiltInServerCommand extends Command
{
    use InteractsWithSoloConfig;

    /** Matches the HTTP line whether commented or not, tolerant of quote, spacing, and value variations. */
    private const HTTP_ENTRY_PATTERN = '/^(\h*)(?:(\/\/)\h*)?([\'\"])HTTP\3(\h*=>\h*)(.+,)\h*$/m';

    protected $signature = 'starter-kit-setup:using-built-in-server';

    protected $description = 'Configure soloterm/solo config file for using the built-in HTTP server.';

    public function handle(): int
    {
        $content = $this->readSoloConfigContent();

        if ($content === null) {
            return self::FAILURE;
        }

        $usingBuiltInServer = confirm(
            label: 'Are you using the built-in HTTP server?',
            default: true
        );

        if ($usingBuiltInServer) {
            if (preg_match(self::HTTP_ENTRY_PATTERN, $content, $matches) === 1 && $matches[2] === '') {
                $this->info('Great! No changes needed.');

                return self::SUCCESS;
            }

            $updated = preg_replace(self::HTTP_ENTRY_PATTERN, '$1$3HTTP$3$4$5', $content, 1, $replacements);
        } else {
            if (preg_match(self::HTTP_ENTRY_PATTERN, $content, $matches) === 1 && $matches[2] !== '') {
                $this->info('Great! No changes needed.');

                return self::SUCCESS;
            }

            $updated = preg_replace(self::HTTP_ENTRY_PATTERN, '$1// $3HTTP$3$4$5', $content, 1, $replacements);
        }

        if ($replacements === 0 || $updated === null) {
            $this->error('HTTP command entry not found in solo.php configuration.');

            return self::FAILURE;
        }

        if (! $this->writeSoloConfigContent($updated)) {
            return self::FAILURE;
        }

        $message = $usingBuiltInServer
            ? 'Successfully enabled HTTP server in solo.php configuration.'
            : 'Successfully disabled HTTP server in solo.php configuration.';

        $this->info($message);

        return self::SUCCESS;
    }
}
