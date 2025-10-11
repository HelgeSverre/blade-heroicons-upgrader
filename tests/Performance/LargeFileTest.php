<?php

use HelgeSverre\BladeHeroiconsUpgrader\IconReplacer;

it('handles large files with 10k+ lines efficiently', function () {
    $replacer = new IconReplacer(['archive' => 'archive-box']);

    // Generate a large file with 10,000 lines
    $lines = [];
    for ($i = 0; $i < 10000; $i++) {
        $lines[] = "<div>Line $i <x-heroicon-o-archive /> content</div>";
    }
    $largeContent = implode("\n", $lines);

    $start = microtime(true);
    $result = $replacer->replaceIcons($largeContent);
    $duration = microtime(true) - $start;

    // Verify all replacements were made
    expect($result->count())->toBe(10000)
        ->and($result->new)->toContain('heroicon-o-archive-box')
        ->and($result->new)->not->toContain('<x-heroicon-o-archive />')
        ->and($duration)->toBeLessThan(60); // Should complete in less than 60 seconds
});

it('handles files with 100+ icon replacements', function () {
    $replacer = new IconReplacer([
        'archive' => 'archive-box',
        'bell' => 'bell',
        'cake' => 'cake',
    ]);

    // Generate content with 100 icons
    $icons = [
        '<x-heroicon-o-archive />',
        '<x-heroicon-s-archive />',
        '<x-heroicon-m-archive />',
    ];

    $parts = [];
    for ($i = 0; $i < 100; $i++) {
        $parts[] = $icons[$i % 3];
    }
    $content = implode(' ', $parts);

    $start = microtime(true);
    $result = $replacer->replaceIcons($content);
    $duration = microtime(true) - $start;

    expect($result->count())->toBe(100)
        ->and($duration)->toBeLessThan(2); // Should be very fast
});

it('handles deep directory structures efficiently', function () {
    // This test validates that the file finding logic doesn't get slow with deep nesting
    $tempDir = sys_get_temp_dir().'/heroicons-perf-test-'.uniqid();

    // Create 10 levels of nested directories
    $currentPath = $tempDir;
    for ($i = 0; $i < 10; $i++) {
        $currentPath .= "/level$i";
        mkdir($currentPath, 0755, true);

        // Add a test file at each level
        file_put_contents(
            "$currentPath/test$i.blade.php",
            "<x-heroicon-o-archive /> content at level $i"
        );
    }

    // Simulate what the command does
    $allFiles = collect([]);
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($tempDir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    $start = microtime(true);
    foreach ($iterator as $file) {
        if ($file->isFile()) {
            $allFiles->push($file->getPathname());
        }
    }
    $duration = microtime(true) - $start;

    expect($allFiles->count())->toBe(10)
        ->and($duration)->toBeLessThan(1); // Should find files quickly

    // Cleanup
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($tempDir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    foreach ($files as $file) {
        $file->isDir() ? rmdir($file->getRealPath()) : unlink($file->getRealPath());
    }
    rmdir($tempDir);
});

it('processes multiple variants efficiently', function () {
    // Test with all icon mappings from config
    $iconsMap = config('blade-heroicons-upgrader.replacements');
    $replacer = new IconReplacer($iconsMap);

    // Create content with multiple different icons (using icons that actually get replaced)
    // 'archive' -> 'archive-box', 'menu' -> 'bars-3' are real mappings in the config
    $content = '<x-heroicon-o-archive /> <x-heroicon-o-menu /> ';
    $content .= '<x-heroicon-s-archive /> <x-heroicon-s-menu /> ';
    $content .= '<x-heroicon-m-archive /> <x-heroicon-m-menu />';

    $start = microtime(true);
    $result = $replacer->replaceIcons($content);
    $duration = microtime(true) - $start;

    expect($result->count())->toBe(6)
        ->and($duration)->toBeLessThan(1);
});

it('handles files with no matches efficiently', function () {
    $replacer = new IconReplacer(['archive' => 'archive-box']);

    // Large file with no icon references
    $lines = [];
    for ($i = 0; $i < 10000; $i++) {
        $lines[] = "<div>Line $i with no icons here</div>";
    }
    $content = implode("\n", $lines);

    $start = microtime(true);
    $result = $replacer->replaceIcons($content);
    $duration = microtime(true) - $start;

    expect($result->count())->toBe(0)
        ->and($result->isEmpty())->toBeTrue()
        ->and($duration)->toBeLessThan(2); // Should skip quickly
});

it('handles mixed content types at scale', function () {
    $replacer = new IconReplacer(['archive' => 'archive-box']);

    // Mix of components, directives, and strings
    $lines = [];
    for ($i = 0; $i < 1000; $i++) {
        $type = $i % 3;
        match ($type) {
            0 => $lines[] = '<x-heroicon-o-archive />',
            1 => $lines[] = "@svg('heroicon-o-archive')",
            2 => $lines[] = '["icon" => "heroicon-o-archive"]',
        };
    }
    $content = implode("\n", $lines);

    $start = microtime(true);
    $result = $replacer->replaceIcons($content);
    $duration = microtime(true) - $start;

    expect($result->count())->toBe(1000)
        ->and($duration)->toBeLessThan(3);
});

it('maintains reasonable memory usage with large files', function () {
    $replacer = new IconReplacer(['archive' => 'archive-box']);

    $memoryBefore = memory_get_usage(true);

    // Process a moderately large file
    $lines = [];
    for ($i = 0; $i < 5000; $i++) {
        $lines[] = "<div>Line $i <x-heroicon-o-archive /></div>";
    }
    $content = implode("\n", $lines);

    $result = $replacer->replaceIcons($content);

    $memoryAfter = memory_get_usage(true);
    $memoryUsed = ($memoryAfter - $memoryBefore) / 1024 / 1024; // Convert to MB

    expect($result->count())->toBe(5000)
        ->and($memoryUsed)->toBeLessThan(50); // Should use less than 50MB
});
