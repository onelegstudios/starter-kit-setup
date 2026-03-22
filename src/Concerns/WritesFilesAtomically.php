<?php

namespace Onelegstudios\StarterKitSetup\Concerns;

trait WritesFilesAtomically
{
    protected function replaceFileContent(string $path, string $content): bool
    {
        $directory = dirname($path);
        $temporaryPath = @tempnam($directory, basename($path).'.tmp.');

        if ($temporaryPath === false) {
            return false;
        }

        if (dirname($temporaryPath) !== $directory) {
            @unlink($temporaryPath);

            return false;
        }

        try {
            try {
                $bytesWritten = file_put_contents($temporaryPath, $content);
            } catch (\ErrorException) {
                return false;
            }

            if ($bytesWritten !== strlen($content)) {
                return false;
            }

            if (@rename($temporaryPath, $path)) {
                return true;
            }

            if (! @copy($temporaryPath, $path)) {
                return false;
            }

            return file_get_contents($path) === $content;
        } finally {
            if (is_file($temporaryPath)) {
                @unlink($temporaryPath);
            }
        }
    }
}
