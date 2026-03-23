<?php

namespace Onelegstudios\StarterKitSetup\Tests;

final class FilesystemFakes
{
    /** @var array<string, int> */
    private static array $partialWritesByContent = [];

    private static bool $renameShouldFail = false;

    public static function reset(): void
    {
        self::$partialWritesByContent = [];
        self::$renameShouldFail = false;
    }

    public static function failRename(): void
    {
        self::$renameShouldFail = true;
    }

    public static function addPartialWrite(string $content, int $bytesWritten): void
    {
        self::$partialWritesByContent[$content] = $bytesWritten;
    }

    public static function filePutContents(string $filename, string $content, int $flags = 0, mixed $context = null): int|false
    {
        $partialWriteKey = self::partialWriteKeyFor($content);

        if ($partialWriteKey !== null) {
            $bytesWritten = self::$partialWritesByContent[$partialWriteKey];
            unset(self::$partialWritesByContent[$partialWriteKey]);

            \file_put_contents($filename, substr($content, 0, $bytesWritten), $flags, $context);

            return $bytesWritten;
        }

        return \file_put_contents($filename, $content, $flags, $context);
    }

    public static function rename(string $from, string $to): bool
    {
        if (self::$renameShouldFail) {
            self::$renameShouldFail = false;

            return false;
        }

        return \rename($from, $to);
    }

    private static function partialWriteKeyFor(string $content): ?string
    {
        if (array_key_exists($content, self::$partialWritesByContent)) {
            return $content;
        }

        $normalizedContent = self::normalizeLineEndings($content);

        foreach (array_keys(self::$partialWritesByContent) as $registeredContent) {
            if (self::normalizeLineEndings($registeredContent) === $normalizedContent) {
                return $registeredContent;
            }
        }

        return null;
    }

    private static function normalizeLineEndings(string $content): string
    {
        return str_replace("\r\n", "\n", $content);
    }
}

namespace Onelegstudios\StarterKitSetup\Concerns;

use Onelegstudios\StarterKitSetup\Tests\FilesystemFakes;

function file_put_contents(string $filename, string $data, int $flags = 0, mixed $context = null): int|false
{
    return FilesystemFakes::filePutContents($filename, $data, $flags, $context);
}

function rename(string $from, string $to): bool
{
    return FilesystemFakes::rename($from, $to);
}
