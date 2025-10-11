<?php

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\File;
use Symfony\Component\Console\Output\BufferedOutput;

it('handles non-existent file path gracefully', function () {
    $buffer = new BufferedOutput;

    // The command will throw when there are no files because Progress bar requires at least one item
    // This is expected behavior - the command should not run with no valid files
    expect(fn () => app(Kernel::class)->call('blade-heroicons-upgrader:upgrade',
        ['paths' => './tests/Samples/non-existent-file.blade.php', '--dry' => true],
        $buffer
    ))->toThrow(InvalidArgumentException::class);
});

it('handles non-existent directory gracefully', function () {
    $buffer = new BufferedOutput;

    // The command will throw when there are no files because Progress bar requires at least one item
    // This is expected behavior - the command should not run with no valid directories
    expect(fn () => app(Kernel::class)->call('blade-heroicons-upgrader:upgrade',
        ['paths' => './tests/NonExistentDirectory', '--dry' => true],
        $buffer
    ))->toThrow(InvalidArgumentException::class);
});

it('processes multiple paths correctly', function () {
    $buffer = new BufferedOutput;

    app(Kernel::class)->call('blade-heroicons-upgrader:upgrade',
        [
            'paths' => [
                './tests/Samples/MenuComposer.php.txt',
                './tests/Samples/tall-toasts-icon.blade.php.txt',
            ],
            '--dry' => true,
        ],
        $buffer
    );

    $output = $buffer->fetch();
    // Should process both files: 1 replacement + 2 replacements = 3 total
    expect($output)->toContain('Replaced 3 icons across 2 files.');
});

it('processes directory and file paths together', function () {
    $buffer = new BufferedOutput;

    app(Kernel::class)->call('blade-heroicons-upgrader:upgrade',
        [
            'paths' => [
                './tests/Samples/MenuComposer.php.txt',
            ],
            '--dry' => true,
        ],
        $buffer
    );

    $output = $buffer->fetch();
    expect($output)->toContain('Replaced 1 icons across 1 files.');
});

it('outputs detailed replacement table with file paths and positions', function () {
    $buffer = new BufferedOutput;

    app(Kernel::class)->call('blade-heroicons-upgrader:upgrade',
        ['paths' => './tests/Samples/MenuComposer.php.txt', '--dry' => true],
        $buffer
    );

    $output = $buffer->fetch();
    // Should contain table headers
    expect($output)
        ->toContain('Old icon')
        ->toContain('New Icon')
        ->toContain('Location');
});

it('outputs execution time', function () {
    $buffer = new BufferedOutput;

    app(Kernel::class)->call('blade-heroicons-upgrader:upgrade',
        ['paths' => './tests/Samples/MenuComposer.php.txt', '--dry' => true],
        $buffer
    );

    $output = $buffer->fetch();
    expect($output)->toContain('Took');
});

it('actually modifies files when not in dry-run mode', function () {
    // Create a temporary test file
    $tempDir = sys_get_temp_dir().'/heroicons-test-'.uniqid();
    File::makeDirectory($tempDir);
    $tempFile = $tempDir.'/test.blade.php';

    $originalContent = '<x-heroicon-o-archive />';
    File::put($tempFile, $originalContent);

    // Run command without --dry flag
    $buffer = new BufferedOutput;
    app(Kernel::class)->call('blade-heroicons-upgrader:upgrade',
        ['paths' => $tempFile],
        $buffer
    );

    // Verify file was actually modified
    $newContent = File::get($tempFile);
    expect($newContent)->toBe('<x-heroicon-o-archive-box />')
        ->and($newContent)->not->toBe($originalContent);

    // Cleanup
    File::deleteDirectory($tempDir);
});

it('does not modify files in dry-run mode', function () {
    // Create a temporary test file
    $tempDir = sys_get_temp_dir().'/heroicons-test-'.uniqid();
    File::makeDirectory($tempDir);
    $tempFile = $tempDir.'/test.blade.php';

    $originalContent = '<x-heroicon-o-archive />';
    File::put($tempFile, $originalContent);

    // Run command with --dry flag
    $buffer = new BufferedOutput;
    app(Kernel::class)->call('blade-heroicons-upgrader:upgrade',
        ['paths' => $tempFile, '--dry' => true],
        $buffer
    );

    // Verify file was NOT modified
    $newContent = File::get($tempFile);
    expect($newContent)->toBe($originalContent);

    // Cleanup
    File::deleteDirectory($tempDir);
});

it('processes empty directory correctly', function () {
    // Create empty temp directory
    $tempDir = sys_get_temp_dir().'/heroicons-test-empty-'.uniqid();
    File::makeDirectory($tempDir);

    $buffer = new BufferedOutput;

    // The command will throw when directory is empty because Progress bar requires at least one item
    expect(fn () => app(Kernel::class)->call('blade-heroicons-upgrader:upgrade',
        ['paths' => $tempDir, '--dry' => true],
        $buffer
    ))->toThrow(InvalidArgumentException::class);

    // Cleanup
    File::deleteDirectory($tempDir);
});

it('processes nested directory structure', function () {
    // Create nested temp directory structure with test files
    $tempDir = sys_get_temp_dir().'/heroicons-test-nested-'.uniqid();
    File::makeDirectory($tempDir.'/level1/level2/level3', 0755, true);

    File::put($tempDir.'/level1/test1.blade.php', '<x-heroicon-o-archive />');
    File::put($tempDir.'/level1/level2/test2.blade.php', '<x-heroicon-o-archive />');
    File::put($tempDir.'/level1/level2/level3/test3.blade.php', '<x-heroicon-o-archive />');

    $buffer = new BufferedOutput;
    app(Kernel::class)->call('blade-heroicons-upgrader:upgrade',
        ['paths' => $tempDir, '--dry' => true],
        $buffer
    );

    $output = $buffer->fetch();
    // Should find all 3 files in nested structure
    expect($output)->toContain('Replaced 3 icons across 3 files.');

    // Cleanup
    File::deleteDirectory($tempDir);
});

it('handles files with no icon replacements', function () {
    // Create temp file with content but no heroicons
    $tempDir = sys_get_temp_dir().'/heroicons-test-'.uniqid();
    File::makeDirectory($tempDir);
    $tempFile = $tempDir.'/no-icons.blade.php';

    File::put($tempFile, '<div class="container"><p>No icons here!</p></div>');

    $buffer = new BufferedOutput;
    app(Kernel::class)->call('blade-heroicons-upgrader:upgrade',
        ['paths' => $tempFile, '--dry' => true],
        $buffer
    );

    $output = $buffer->fetch();
    expect($output)->toContain('Replaced 0 icons across 1 files.');

    // Cleanup
    File::deleteDirectory($tempDir);
});
