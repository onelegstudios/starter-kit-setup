<?php

namespace Onelegstudios\StarterKitSetup\Commands;

use Illuminate\Console\Command;

use function Laravel\Prompts\confirm;

class UsingHerdCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'starter-kit-setup:using-herd';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Configure soloterm/solo config file for using Herd or artisan serve.';

    /**
     * Execute the console command.
     */
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

        $content = file_get_contents($configPath);

        if ($content === false) {
            $this->error('Unable to read config file solo.php.');

            return self::FAILURE;
        }

        if ($usingHerd) {
            $updated = str_replace(
                "        'HTTP' => 'php artisan serve',",
                "        // 'HTTP' => 'php artisan serve',",
                $content
            );

            if ($content === $updated) {
                $this->info('Great! No changes needed.');

                return self::SUCCESS;
            }

            file_put_contents($configPath, $updated);
            $this->info('Successfully disabled HTTP server in solo.php configuration.');

            return self::SUCCESS;
        }

        $updated = str_replace(
            "        // 'HTTP' => 'php artisan serve',",
            "        'HTTP' => 'php artisan serve',",
            $content
        );

        if ($content === $updated) {
            $this->warn('The HTTP server line is already uncommented or not found.');

            return self::SUCCESS;
        }

        file_put_contents($configPath, $updated);
        $this->info('Successfully enabled HTTP server in solo.php configuration.');

        return self::SUCCESS;
    }
}
