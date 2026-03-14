<?php

namespace Onelegstudios\StarterKitSetup\Commands;

use Illuminate\Console\Command;

use function Laravel\Prompts\confirm;

class UsingHerdCommand extends Command
{
    private const SOLO_HTTP_LINE = "        'HTTP' => 'php artisan serve',";

    private const SOLO_HTTP_LINE_COMMENTED = "        // 'HTTP' => 'php artisan serve',";

    protected $signature = 'starter-kit-setup:using-herd';

    protected $description = 'Configure soloterm/solo config file for using Herd or artisan serve.';

    public function handle(): int
    {
        $usingHerd = confirm(
            label: 'Are you using Laravel Herd?',
            default: true
        );

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

        $search = $usingHerd ? self::SOLO_HTTP_LINE : self::SOLO_HTTP_LINE_COMMENTED;
        $replace = $usingHerd ? self::SOLO_HTTP_LINE_COMMENTED : self::SOLO_HTTP_LINE;

        $updated = str_replace($search, $replace, $content, $replacements);

        if ($replacements === 0) {
            $message = $usingHerd
                ? 'Great! No changes needed.'
                : 'The HTTP server line is already uncommented or not found.';

            $this->info($message);

            return self::SUCCESS;
        }

        file_put_contents($configPath, $updated);

        $message = $usingHerd
            ? 'Successfully disabled HTTP server in solo.php configuration.'
            : 'Successfully enabled HTTP server in solo.php configuration.';

        $this->info($message);

        return self::SUCCESS;
    }
}
