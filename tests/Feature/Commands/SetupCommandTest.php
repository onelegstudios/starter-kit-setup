<?php

use Onelegstudios\StarterKitSetup\Tests\FilesystemFakes;
use Orchestra\Testbench\Concerns\WithWorkbench;

uses(WithWorkbench::class);

beforeEach(function (): void {
    FilesystemFakes::reset();
    starterKitResetSoloConfigPath();
});

afterEach(function (): void {
    FilesystemFakes::reset();
    starterKitResetSoloConfigPath();
});

test('setup command executes all setup commands', function () {
    $configPath = starterKitSoloConfigPath();
    $templateContent = starterKitSoloTemplateContent();

    $content = str_replace(
        "        // 'HTTP' => 'php artisan serve',",
        "        'HTTP' => 'php artisan serve',",
        $templateContent
    );

    starterKitWriteSoloConfig($content);

    starterKitArtisan('starter-kit-setup:setup')
        ->expectsOutput('Running starter-kit-setup commands...')
        ->expectsConfirmation('Are you using the built-in HTTP server?', 'no')
        ->expectsOutput('Successfully disabled HTTP server in solo.php configuration.')
        ->expectsOutput('Successfully added Mailpit command to solo.php configuration.')
        ->expectsOutput('All starter-kit-setup commands completed successfully.')
        ->assertExitCode(0);

    $updatedContent = starterKitReadFile($configPath);
    $this->assertStringContainsString("        // 'HTTP' => 'php artisan serve',", $updatedContent);
    $this->assertStringContainsString("        'Mailpit' => Command::from('mailpit')->lazy(),", $updatedContent);
});

test('setup command fails when first command fails', function () {
    starterKitArtisan('starter-kit-setup:setup')
        ->expectsOutput('Running starter-kit-setup commands...')
        ->expectsOutput('Config file solo.php not found.')
        ->expectsOutput('The using-built-in-server command failed.')
        ->assertExitCode(1);
});

test('setup command still succeeds when mailpit anchor comment is missing', function () {
    $templateContent = starterKitSoloTemplateContent();

    $contentWithoutMailpitAnchor = str_replace(
        '        // Lazy commands do not automatically start when Solo starts.',
        '',
        $templateContent
    );

    starterKitWriteSoloConfig($contentWithoutMailpitAnchor);

    starterKitArtisan('starter-kit-setup:setup')
        ->expectsOutput('Running starter-kit-setup commands...')
        ->expectsConfirmation('Are you using the built-in HTTP server?', 'yes')
        ->expectsOutput('Successfully enabled HTTP server in solo.php configuration.')
        ->expectsOutput('Successfully added Mailpit command to solo.php configuration.')
        ->expectsOutput('All starter-kit-setup commands completed successfully.')
        ->assertExitCode(0);

    $updatedContent = starterKitReadFile(starterKitSoloConfigPath());
    $this->assertStringContainsString("        'Mailpit' => Command::from('mailpit')->lazy(),", $updatedContent);
});

test('solo config helper throws when file cannot be written', function () {
    $configPath = starterKitSoloConfigPath();
    mkdir($configPath);

    expect(fn () => starterKitWriteSoloConfig('test content'))
        ->toThrow(RuntimeException::class, "Unable to write file: {$configPath}");
});

test('setup command fails when rollback write is incomplete', function () {
    $configPath = starterKitSoloConfigPath();
    $templateContent = starterKitSoloTemplateContent();

    $content = str_replace(
        [
            "        // 'HTTP' => 'php artisan serve',",
            '        // Lazy commands do not automatically start when Solo starts.',
            "    'commands' => [",
        ],
        [
            "        'HTTP' => 'php artisan serve',",
            '',
            "    'lazy_commands' => [",
        ],
        $templateContent
    );

    starterKitWriteSoloConfig($content);
    FilesystemFakes::addPartialWrite($content, 10);

    starterKitArtisan('starter-kit-setup:setup')
        ->expectsOutput('Running starter-kit-setup commands...')
        ->expectsConfirmation('Are you using the built-in HTTP server?', 'no')
        ->expectsOutput('Successfully disabled HTTP server in solo.php configuration.')
        ->expectsOutput('Unable to update solo.php: insertion anchor not found.')
        ->expectsOutput('Rollback failed after add-mailpit command failed.')
        ->assertExitCode(1);

    $updatedContent = starterKitReadFile($configPath);
    $this->assertNotSame('', $updatedContent);
    $this->assertStringContainsString("        // 'HTTP' => 'php artisan serve',", $updatedContent);
    $this->assertStringNotContainsString("        'Mailpit' => Command::from('mailpit')->lazy(),", $updatedContent);
});
