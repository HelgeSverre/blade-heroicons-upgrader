<?php

use HelgeSverre\BladeHeroiconsUpgrader\IconReplacer;

it('runs the tests', function () {
    expect(true)->toBeTrue();
});

// Test for Blade component syntax
it('replaces heroicons in specific scenarios', function ($originalContent, $expectedContent) {
    $replacer = new IconReplacer();
    $iconsMap = config('blade-heroicons-upgrader.replacements');

    $replacedContent = $replacer->withIconMap($iconsMap)->replaceIcons($originalContent);

    expect($replacedContent->new)->toBe($expectedContent)
        ->and($replacedContent->count())->toBe(1);
})->with('RealWorld');

// Test for Blade component syntax
it('replaces heroicons correctly when used as component', function ($originalContent, $expectedContent) {
    $replacer = new IconReplacer();
    $iconsMap = ['old-icon-name' => 'new-icon-name'];

    $replacedContent = $replacer->withIconMap($iconsMap)->replaceIcons($originalContent);

    expect($replacedContent->new)->toBe($expectedContent)
        ->and($replacedContent->count())->toBe(1);
})->with('BladeComponents');

// Test for SVG directive syntax
it('replaces heroicons correctly when used as @svg directive', function ($originalContent, $expectedContent) {
    $replacer = new IconReplacer();
    $iconsMap = ['old-icon-name' => 'new-icon-name'];

    $replacedContent = $replacer->withIconMap($iconsMap)->replaceIcons($originalContent);

    expect($replacedContent->new)->toBe($expectedContent)
        ->and($replacedContent->count())->toBe(1);
})->with('SvgDirectives');

// Test for quoted string syntax
it('replaces heroicons correctly when used inside a quoted string', function ($originalContent, $expectedContent) {
    $replacer = new IconReplacer();
    $iconsMap = ['old-icon-name' => 'new-icon-name'];

    $replacedContent = $replacer->withIconMap($iconsMap)->replaceIcons($originalContent);

    expect($replacedContent->new)->toBe($expectedContent)
        ->and($replacedContent->count())->toBe(1);
})->with('Strings');

it('quick test', function () {
    $replacer = new IconReplacer();
    $iconsMap = ['file-download' => 'document-arrow-down'];

    // Blade component
    $replaced = $replacer->withIconMap($iconsMap)->replaceIcons('stuff before <x-heroicon-o-file-download /> stuff after');
    expect($replaced->new)->toBe('stuff before <x-heroicon-o-document-arrow-down /> stuff after');

    // Blade component
    $replaced = $replacer->withIconMap($iconsMap)->replaceIcons('<x-heroicon-o-file-download />');
    expect($replaced->new)->toBe('<x-heroicon-o-document-arrow-down />');

    // Blade component w newline
    $replaced = $replacer->withIconMap($iconsMap)->replaceIcons("<x-heroicon-o-file-download\n />");
    expect($replaced->new)->toBe("<x-heroicon-o-document-arrow-down\n />");

    // SVG Directive
    $replaced = $replacer->withIconMap($iconsMap)->replaceIcons("@svg('heroicon-o-file-download')");
    expect($replaced->new)->toBe("@svg('heroicon-o-document-arrow-down')");

    // Double quotes
    $replaced = $replacer->withIconMap($iconsMap)->replaceIcons('@svg("heroicon-o-file-download")');
    expect($replaced->new)->toBe('@svg("heroicon-o-document-arrow-down")');

    // Single quotes
    $replaced = $replacer->withIconMap($iconsMap)->replaceIcons('@svg("heroicon-o-file-download")');
    expect($replaced->new)->toBe('@svg("heroicon-o-document-arrow-down")');

    // in php file double quotes
    $replaced = $replacer->withIconMap($iconsMap)->replaceIcons('$icon = "heroicon-o-file-download";');
    expect($replaced->new)->toBe('$icon = "heroicon-o-document-arrow-down";');

});

it('it replaces 1', function () {
    $replacer = new IconReplacer();
    $iconsMap = ['old-icon-name' => 'new-icon-name'];

    $originalContent = ' Text with "\'heroicon-o-old-icon-name" inside ';
    $expectedContent = ' Text with "\'heroicon-o-new-icon-name" inside ';

    $replacedContent = $replacer->withIconMap($iconsMap)->replaceIcons($originalContent);

    expect($replacedContent->new)->toBe($expectedContent)
        ->and($replacedContent->count())->toBe(1);
});

it('it replaces 2', function () {
    $replacer = new IconReplacer();
    $iconsMap = ['old-icon-name' => 'new-icon-name'];

    $originalContent = ' Text with "\'heroicon-o-old-icon-name" inside ';
    $expectedContent = ' Text with "\'heroicon-o-new-icon-name" inside ';

    $replacedContent = $replacer->withIconMap($iconsMap)->replaceIcons($originalContent);

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

    $replacedContent = $replacer->withIconMap($iconsMap)->replaceIcons($originalContent);

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

    $replacedContent = $replacer->withIconMap($iconsMap)->replaceIcons($originalContent);

    expect($replacedContent->new)->toBe($expectedContent)
        ->and($replacedContent->count())->toBe(2);
});

it('it replaces without overwriting previously replaced icon', function () {
    $replacer = new IconReplacer();
    $iconsMap = [
        'server' => 'server-stack',
        'check' => 'checkmark',
    ];
    $originalContent = ' <x-heroicon-o-server-stack />   <x-heroicon-o-server />  ';
    $expectedContent = ' <x-heroicon-o-server-stack />   <x-heroicon-o-server-stack />  ';

    // Run it twice to make sure it doesn't replace the previously replaced icon
    $replacedContent = $replacer->withIconMap($iconsMap)->replaceIcons($originalContent);
    expect($replacedContent->new)->toBe($expectedContent)->and($replacedContent->count())->toBe(1);
    $replacedContent = $replacer->replaceIcons($replacedContent->new, $iconsMap);

    expect($replacedContent->new)->toBe($expectedContent)->and($replacedContent->count())->toBe(0);

    $originalContent = ' Text with "\'heroicon-m-check" ';
    $expectedContent = ' Text with "\'heroicon-m-checkmark" ';

    $replacedContent = $replacer->withIconMap($iconsMap)->replaceIcons($originalContent);
    expect($replacedContent->new)->toBe($expectedContent)->and($replacedContent->count())->toBe(1);
    $replacedContent = $replacer->replaceIcons($replacedContent->new, $iconsMap);
    expect($replacedContent->new)->toBe($expectedContent)->and($replacedContent->count())->toBe(0);

});
