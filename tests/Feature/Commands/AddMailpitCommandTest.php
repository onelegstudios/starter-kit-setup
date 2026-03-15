<?php

use function Orchestra\Testbench\workbench_path;
use Orchestra\Testbench\Concerns\WithWorkbench;

uses(WithWorkbench::class);

test('command adds mailpit line to solo config', function () {
    $configPath      = config_path('solo.php');
    $templatePath    = workbench_path('config/solo.php');
    $templateContent = file_get_contents($templatePath);

    file_put_contents($configPath, $templateContent);

    try {
        $this->artisan('starter-kit-setup:add-mailpit')
            ->expectsOutput('Successfully added Mailpit command to solo.php configuration.')
            ->assertExitCode(0);

        $updatedContent = file_get_contents($configPath);
        $this->assertStringContainsString("        'Mailpit' => Command::from('mailpt')->lazy(),", $updatedContent);
        $this->assertSame(1, substr_count($updatedContent, "        'Mailpit' => Command::from('mailpt')->lazy(),"));
    } finally {
        if (file_exists($configPath)) {
            unlink($configPath);
        }
    }
});

test('command is idempotent when mailpit line already exists', function () {
    $configPath      = config_path('solo.php');
    $templatePath    = workbench_path('config/solo.php');
    $templateContent = file_get_contents($templatePath);

    $contentWithMailpit = str_replace(
        '        // Lazy commands do not automatically start when Solo starts.',
        "        'Mailpit' => Command::from('mailpt')->lazy(),\n\n        // Lazy commands do not automatically start when Solo starts.",
        $templateContent
    );
    file_put_contents($configPath, $contentWithMailpit);

    try {
        $this->artisan('starter-kit-setup:add-mailpit')
            ->expectsOutput('Mailpit command is already present in solo.php configuration.')
            ->assertExitCode(0);

        $updatedContent = file_get_contents($configPath);
        $this->assertSame(1, substr_count($updatedContent, "        'Mailpit' => Command::from('mailpt')->lazy(),"));
    } finally {
        if (file_exists($configPath)) {
            unlink($configPath);
        }
    }
});

test('command fails when config file not found', function () {
    $configPath = config_path('solo.php');

    if (file_exists($configPath)) {
        unlink($configPath);
    }

    try {
        $this->artisan('starter-kit-setup:add-mailpit')
            ->expectsOutput('Config file solo.php not found.')
            ->assertExitCode(1);
    } finally {
        if (file_exists($configPath)) {
            unlink($configPath);
        }
    }
});

test('command fails when config path cannot be read as file', function () {
    $configPath = config_path('solo.php');

    if (file_exists($configPath)) {
        unlink($configPath);
    }

    mkdir($configPath);

    try {
        $this->artisan('starter-kit-setup:add-mailpit')
            ->expectsOutput('Unable to read config file solo.php.')
            ->assertExitCode(1);
    } finally {
        if (is_dir($configPath)) {
            rmdir($configPath);
        }
    }
});

test('command fails when insertion anchor is not found', function () {
    $configPath      = config_path('solo.php');
    $templatePath    = workbench_path('config/solo.php');
    $templateContent = file_get_contents($templatePath);

    $contentWithoutAnchor = str_replace(
        '        // Lazy commands do not automatically start when Solo starts.',
        '',
        $templateContent
    );
    file_put_contents($configPath, $contentWithoutAnchor);

    try {
        $this->artisan('starter-kit-setup:add-mailpit')
            ->expectsOutput('Unable to update solo.php: insertion anchor not found.')
            ->assertExitCode(1);
    } finally {
        if (file_exists($configPath)) {
            unlink($configPath);
        }
    }
});
