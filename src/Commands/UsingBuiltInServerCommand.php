<?php

namespace Onelegstudios\StarterKitSetup\Commands;

use Illuminate\Console\Command;
use Onelegstudios\StarterKitSetup\Concerns\InteractsWithSoloConfig;

use function Laravel\Prompts\confirm;

class UsingBuiltInServerCommand extends Command
{
    use InteractsWithSoloConfig;

    /** Matches the HTTP line whether commented or not, tolerant of whitespace variations. */
    private const HTTP_UNCOMMENTED_PATTERN = '/^(\h*)\'HTTP\'\h*=>\h*\'php artisan serve\',/m';

    private const HTTP_COMMENTED_PATTERN = '/^(\h*)\/\/\h*\'HTTP\'\h*=>\h*\'php artisan serve\',/m';

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
            if (preg_match(self::HTTP_UNCOMMENTED_PATTERN, $content)) {
                $this->info('Great! No changes needed.');

                return self::SUCCESS;
            }

            $updated = preg_replace(self::HTTP_COMMENTED_PATTERN, '$1'."'HTTP' => 'php artisan serve',", $content, -1, $replacements);
        } else {
            if (preg_match(self::HTTP_COMMENTED_PATTERN, $content)) {
                $this->info('Great! No changes needed.');

                return self::SUCCESS;
            }

            $updated = preg_replace(self::HTTP_UNCOMMENTED_PATTERN, '$1'."// 'HTTP' => 'php artisan serve',", $content, -1, $replacements);
        }

        if ($replacements === 0 || $updated === null) {
            $this->info('Great! No changes needed.');

            return self::SUCCESS;
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
