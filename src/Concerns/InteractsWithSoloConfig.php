<?php

namespace Onelegstudios\StarterKitSetup\Concerns;

trait InteractsWithSoloConfig
{
    use WritesFilesAtomically;

    protected function soloConfigPath(): string
    {
        return config_path('solo.php');
    }

    protected function readSoloConfigContent(): ?string
    {
        $configPath = $this->soloConfigPath();

        if (! file_exists($configPath)) {
            $this->error('Config file solo.php not found.');

            return null;
        }

        if (! is_file($configPath)) {
            $this->error('Unable to read config file solo.php.');

            return null;
        }

        if (! is_readable($configPath)) {
            $this->error('Config file solo.php could not be read.');

            return null;
        }

        $content = file_get_contents($configPath);

        if ($content === false) {
            $this->error('Config file solo.php could not be read.');

            return null;
        }

        return $content;
    }

    protected function writeSoloConfigContent(string $content): bool
    {
        if (! $this->replaceFileContent($this->soloConfigPath(), $content)) {
            $this->error('Unable to update solo.php: write failed.');

            return false;
        }

        return true;
    }
}
