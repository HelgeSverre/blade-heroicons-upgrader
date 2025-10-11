<?php

use HelgeSverre\BladeHeroiconsUpgrader\IconReplacer;

it('handles empty icon map', function () {
    $replacer = new IconReplacer([]);
    $content = '<x-heroicon-o-archive /> @svg("heroicon-o-bell")';

    $result = $replacer->replaceIcons($content);

    expect($result->new)->toBe($content)
        ->and($result->count())->toBe(0)
        ->and($result->isEmpty())->toBeTrue();
});

it('skips self-referencing icon mappings', function () {
    $replacer = new IconReplacer([
        'check' => 'check', // Self-referencing
        'archive' => 'archive-box', // Normal mapping
    ]);

    $content = '<x-heroicon-o-check /> <x-heroicon-o-archive />';

    $result = $replacer->replaceIcons($content);

    // Only the 'archive' icon should be replaced, 'check' should be skipped
    expect($result->new)->toBe('<x-heroicon-o-check /> <x-heroicon-o-archive-box />')
        ->and($result->count())->toBe(1);
});

it('handles icon map with icons that map to themselves correctly', function () {
    $replacer = new IconReplacer([
        'bell' => 'bell',
        'home' => 'home',
        'user' => 'user',
    ]);

    $content = '<x-heroicon-o-bell /> <x-heroicon-o-home /> <x-heroicon-o-user />';

    $result = $replacer->replaceIcons($content);

    // Nothing should be replaced since all icons map to themselves
    expect($result->new)->toBe($content)
        ->and($result->count())->toBe(0);
});

it('handles mixed self-referencing and normal mappings', function () {
    $replacer = new IconReplacer([
        'archive' => 'archive-box',
        'check' => 'check', // Self-referencing
        'bell' => 'bell', // Self-referencing
        'cake' => 'cake', // Self-referencing
        'menu' => 'bars-3',
    ]);

    $content = '<x-heroicon-o-archive /> <x-heroicon-o-check /> <x-heroicon-o-bell /> <x-heroicon-o-menu />';

    $result = $replacer->replaceIcons($content);

    // Only 'archive' and 'menu' should be replaced
    expect($result->new)->toBe('<x-heroicon-o-archive-box /> <x-heroicon-o-check /> <x-heroicon-o-bell /> <x-heroicon-o-bars-3 />')
        ->and($result->count())->toBe(2);
});

it('can be constructed with empty map and map set later', function () {
    $replacer = new IconReplacer;
    $content = '<x-heroicon-o-archive />';

    // First run with empty map
    $result1 = $replacer->replaceIcons($content);
    expect($result1->new)->toBe($content)
        ->and($result1->count())->toBe(0);

    // Then set icon map and run again
    $result2 = $replacer->withIconMap(['archive' => 'archive-box'])->replaceIcons($content);
    expect($result2->new)->toBe('<x-heroicon-o-archive-box />')
        ->and($result2->count())->toBe(1);
});

it('withIconMap method allows chaining', function () {
    $replacer = new IconReplacer;
    $iconMap = ['archive' => 'archive-box'];
    $content = '<x-heroicon-o-archive />';

    $result = $replacer
        ->withIconMap($iconMap)
        ->replaceIcons($content);

    expect($result->new)->toBe('<x-heroicon-o-archive-box />')
        ->and($result->count())->toBe(1);
});

it('last icon map wins when set multiple times', function () {
    $replacer = new IconReplacer(['archive' => 'first-icon']);

    $content = '<x-heroicon-o-archive />';

    // Override with new map
    $result = $replacer
        ->withIconMap(['archive' => 'second-icon'])
        ->replaceIcons($content);

    expect($result->new)->toBe('<x-heroicon-o-second-icon />')
        ->and($result->count())->toBe(1);
});

it('handles icon map with special characters in icon names', function () {
    $replacer = new IconReplacer([
        'arrow-sm-up' => 'arrow-small-up',
        'dots-horizontal' => 'ellipsis-horizontal',
    ]);

    $content = '<x-heroicon-o-arrow-sm-up /> <x-heroicon-o-dots-horizontal />';

    $result = $replacer->replaceIcons($content);

    expect($result->new)->toBe('<x-heroicon-o-arrow-small-up /> <x-heroicon-o-ellipsis-horizontal />')
        ->and($result->count())->toBe(2);
});

it('processes all variants for each icon mapping', function () {
    $replacer = new IconReplacer([
        'archive' => 'archive-box',
    ]);

    $content = '<x-heroicon-o-archive /> <x-heroicon-s-archive /> <x-heroicon-m-archive />';

    $result = $replacer->replaceIcons($content);

    // All three variants should be replaced
    expect($result->new)->toBe('<x-heroicon-o-archive-box /> <x-heroicon-s-archive-box /> <x-heroicon-m-archive-box />')
        ->and($result->count())->toBe(3);
});

it('handles icon map from config correctly', function () {
    $iconsMap = config('blade-heroicons-upgrader.replacements');

    $replacer = new IconReplacer($iconsMap);

    $content = '<x-heroicon-o-archive /> <x-heroicon-o-menu />';

    $result = $replacer->replaceIcons($content);

    // Should use real mappings from config
    expect($result->new)->toBe('<x-heroicon-o-archive-box /> <x-heroicon-o-bars-3 />')
        ->and($result->count())->toBe(2)
        ->and($result->isNotEmpty())->toBeTrue();
});
