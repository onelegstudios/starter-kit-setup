<?php

namespace Onelegstudios\StarterKitSetup\Commands;

use Illuminate\Console\Command;

use function Laravel\Prompts\confirm;

class UsingBuiltInServerCommand extends Command
{
    private const SOLO_HTTP_LINE = "        'HTTP' => 'php artisan serve',";

    private const SOLO_HTTP_LINE_COMMENTED = "        // 'HTTP' => 'php artisan serve',";

    protected $signature = 'starter-kit-setup:using-built-in-server';

    protected $description = 'Configure soloterm/solo config file for using the built-in HTTP server or Herd.';

    public function handle(): int
    {
        $usingBuiltInServer = confirm(
            label: 'Are you using the built-in HTTP server?',
            default: false
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

        $search = $usingBuiltInServer ? self::SOLO_HTTP_LINE_COMMENTED : self::SOLO_HTTP_LINE;
        $replace = $usingBuiltInServer ? self::SOLO_HTTP_LINE : self::SOLO_HTTP_LINE_COMMENTED;

        $updated = str_replace($search, $replace, $content, $replacements);

        if ($replacements === 0) {
            $this->info('Great! No changes needed.');

            return self::SUCCESS;
        }

        file_put_contents($configPath, $updated);

        $message = $usingBuiltInServer
            ? 'Successfully enabled HTTP server in solo.php configuration.'
            : 'Successfully disabled HTTP server in solo.php configuration.';

        $this->info($message);

        return self::SUCCESS;
    }
}
