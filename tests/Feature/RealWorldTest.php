<?php

use HelgeSverre\BladeHeroiconsUpgrader\IconReplacer;

it('correctly upgrades icons in files', function ($sampleFile, $expectedReplacement) {

    $replacer = new IconReplacer();
    $iconsMap = config('blade-heroicons-upgrader.replacements');

    $originalContent = file_get_contents($sampleFile);

    $replacedContent = $replacer->replaceIcons($originalContent, $iconsMap);

    expect($replacedContent->count())->toBe($expectedReplacement);

})->with([

    ['./tests/Samples/MenuComposer.php.txt', 1],
    ['./tests/Samples/order-form.blade.php.txt', 7],
    ['./tests/Samples/settings-page.blade.php.txt', 4],
    ['./tests/Samples/tall-toasts-icon.blade.php.txt', 3],
    ['./tests/Samples/watcher.blade.php.txt', 2],

]);
