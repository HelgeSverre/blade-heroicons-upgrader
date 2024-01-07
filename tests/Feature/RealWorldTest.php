<?php

use HelgeSverre\BladeHeroiconsUpgrader\IconReplacer;
use Illuminate\Contracts\Console\Kernel;
use Symfony\Component\Console\Output\BufferedOutput;

it('correctly upgrades icons in files', function ($sampleFile, $expectedReplacement) {

    $replacer = new IconReplacer();
    $iconsMap = config('blade-heroicons-upgrader.replacements');

    $originalContent = file_get_contents($sampleFile);

    $result = $replacer
        ->inFile($sampleFile)
        ->withIconMap($iconsMap)
        ->replaceIcons($originalContent);

    expect($result->count())->toBe($expectedReplacement);

})->with([
    ['./tests/Samples/MenuComposer.php.txt', 1],
    ['./tests/Samples/order-form.blade.php.txt', 7],
    ['./tests/Samples/settings-page.blade.php.txt', 4],
    ['./tests/Samples/tall-toasts-icon.blade.php.txt', 2],
    ['./tests/Samples/watcher.blade.php.txt', 2],
]);

it('can run the command on files and get the same result', function ($sampleFile, $expectedReplacement) {

    $buffer = new BufferedOutput();

    $this->app[Kernel::class]->call('blade-heroicons-upgrader:upgrade',
        ['paths' => $sampleFile, '--dry' => true],
        $buffer

    );

    $output = $buffer->fetch();
    dump($output);

    expect($output)->toContain("Replaced $expectedReplacement icons across 1 files.");

})->with([

    ['./tests/Samples/MenuComposer.php.txt', 1],
    ['./tests/Samples/order-form.blade.php.txt', 7],
    ['./tests/Samples/settings-page.blade.php.txt', 4],
    ['./tests/Samples/tall-toasts-icon.blade.php.txt', 2],
    ['./tests/Samples/watcher.blade.php.txt', 2],

]);
