<?php

namespace Onelegstudios\StarterKitSetup\Tests;

use Illuminate\Foundation\Application;
use Onelegstudios\StarterKitSetup\StarterKitSetupServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    private string $uniqueConfigPath = '';

    protected function tearDown(): void
    {
        if ($this->uniqueConfigPath !== '' && is_dir($this->uniqueConfigPath)) {
            foreach (glob($this->uniqueConfigPath.'/*') ?: [] as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }

            rmdir($this->uniqueConfigPath);
        }

        parent::tearDown();
    }

    /**
     * @param  Application  $app
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            StarterKitSetupServiceProvider::class,
        ];
    }

    /**
     * @param  Application  $app
     */
    public function getEnvironmentSetUp($app): void
    {
        config()->set('database.default', 'testing');

        $this->uniqueConfigPath = sys_get_temp_dir().'/starterkit_test_'.getmypid().'_'.md5(uniqid((string) mt_rand(), true));
        mkdir($this->uniqueConfigPath, 0755, true);
        $app->useConfigPath($this->uniqueConfigPath);

    }
}
