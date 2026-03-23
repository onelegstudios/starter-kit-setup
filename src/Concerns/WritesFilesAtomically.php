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

        if (! $this->isSameDirectory(dirname($temporaryPath), $directory)) {
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

            if ($this->moveFile($temporaryPath, $path)) {
                return true;
            }

            if (file_exists($path)) {
                if (! $this->deleteFile($path)) {
                    return false;
                }

                if ($this->moveFile($temporaryPath, $path)) {
                    return true;
                }
            }

            if (! $this->copyFile($temporaryPath, $path)) {
                return false;
            }

            return $this->readFileContent($path) === $content;
        } finally {
            if (is_file($temporaryPath)) {
                $this->deleteFile($temporaryPath);
            }
        }
    }

    /** @phpstan-impure */
    private function moveFile(string $from, string $to): bool
    {
        return @rename($from, $to);
    }

    private function deleteFile(string $path): bool
    {
        return @unlink($path);
    }

    private function copyFile(string $from, string $to): bool
    {
        return @copy($from, $to);
    }

    private function readFileContent(string $path): string|false
    {
        return file_get_contents($path);
    }

    private function isSameDirectory(string $left, string $right): bool
    {
        $normalizedLeft = $this->normalizePath($left);
        $normalizedRight = $this->normalizePath($right);

        if ($normalizedLeft === $normalizedRight) {
            return true;
        }

        $resolvedLeft = realpath($left);
        $resolvedRight = realpath($right);

        if ($resolvedLeft === false || $resolvedRight === false) {
            return false;
        }

        return $this->normalizePath($resolvedLeft) === $this->normalizePath($resolvedRight);
    }

    private function normalizePath(string $path): string
    {
        $normalizedPath = str_replace('\\', '/', $path);

        return rtrim(strtolower($normalizedPath), '/');
    }
}
