<?php

use HelgeSverre\BladeHeroiconsUpgrader\IconReplacer;

it('runs the tests', function () {
    expect(true)->toBeTrue();
});

// Test for Blade component syntax
it('replaces heroicons correctly when used as component', function ($originalContent, $expectedContent) {
    $replacer = new IconReplacer();
    $iconsMap = ['old-icon-name' => 'new-icon-name'];

    $replacedContent = $replacer->replaceIcons($originalContent, $iconsMap);

    expect($replacedContent->new)->toBe($expectedContent)
        ->and($replacedContent->count())->toBe(1);
})->with('BladeComponents');

// Test for SVG directive syntax
it('replaces heroicons correctly when used as @svg directive', function ($originalContent, $expectedContent) {
    $replacer = new IconReplacer();
    $iconsMap = ['old-icon-name' => 'new-icon-name'];

    $replacedContent = $replacer->replaceIcons($originalContent, $iconsMap);

    expect($replacedContent->new)->toBe($expectedContent)
        ->and($replacedContent->count())->toBe(1);
})->with('SvgDirectives');

// Test for quoted string syntax
it('replaces heroicons correctly when used inside a quoted string', function ($originalContent, $expectedContent) {
    $replacer = new IconReplacer();
    $iconsMap = ['old-icon-name' => 'new-icon-name'];

    $replacedContent = $replacer->replaceIcons($originalContent, $iconsMap);

    expect($replacedContent->new)->toBe($expectedContent)
        ->and($replacedContent->count())->toBe(1);
})->with('Strings');

it('it replaces 1', function () {
    $replacer = new IconReplacer();
    $iconsMap = ['old-icon-name' => 'new-icon-name'];

    $originalContent = ' Text with "\'heroicon-o-old-icon-name" inside ';
    $expectedContent = ' Text with "\'heroicon-o-new-icon-name" inside ';

    $replacedContent = $replacer->replaceIcons($originalContent, $iconsMap);

    expect($replacedContent->new)->toBe($expectedContent)
        ->and($replacedContent->count())->toBe(1);
});

it('it replaces 2', function () {
    $replacer = new IconReplacer();
    $iconsMap = ['old-icon-name' => 'new-icon-name'];

    $originalContent = ' Text with "\'heroicon-o-old-icon-name" inside ';
    $expectedContent = ' Text with "\'heroicon-o-new-icon-name" inside ';

    $replacedContent = $replacer->replaceIcons($originalContent, $iconsMap);

    expect($replacedContent->new)->toBe($expectedContent)
        ->and($replacedContent->count())->toBe(1);
});

it('it replaces multiple variants', function () {
    $replacer = new IconReplacer();
    $iconsMap = [
        'check' => 'checkmark',
        'balloon' => 'ball',
    ];

    $originalContent = ' Text with "\'heroicon-m-check" with "heroicon-o-balloon" inside, but ignore this heroicon-t-user';
    $expectedContent = ' Text with "\'heroicon-m-checkmark" with "heroicon-o-ball" inside, but ignore this heroicon-t-user';

    $replacedContent = $replacer->replaceIcons($originalContent, $iconsMap);

    expect($replacedContent->new)->toBe($expectedContent)
        ->and($replacedContent->count())->toBe(2);
});

it('it replaces with overlap', function () {
    $replacer = new IconReplacer();
    $iconsMap = [
        'check' => 'checkmark',
        'ball' => 'balloon',
    ];

    $originalContent = ' Text with "\'heroicon-m-check" with {[ <div class="heroicon-o-ball"></div> ]} inside, but ignore this heroicon-t-user';
    $expectedContent = ' Text with "\'heroicon-m-checkmark" with {[ <div class="heroicon-o-balloon"></div> ]} inside, but ignore this heroicon-t-user';

    $replacedContent = $replacer->replaceIcons($originalContent, $iconsMap);

    expect($replacedContent->new)->toBe($expectedContent)
        ->and($replacedContent->count())->toBe(2);
});
