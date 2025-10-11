<?php

use HelgeSverre\BladeHeroiconsUpgrader\IconReplacer;

// Test for Blade component syntax
it('replaces heroicons in specific scenarios', function ($originalContent, $expectedContent) {
    $replacer = new IconReplacer;
    $iconsMap = config('blade-heroicons-upgrader.replacements');

    $replacedContent = $replacer->withIconMap($iconsMap)->replaceIcons($originalContent);

    expect($replacedContent->new)->toBe($expectedContent)
        ->and($replacedContent->count())->toBe(1);
})->with('RealWorld');

// Test for Blade component syntax
it('replaces heroicons correctly when used as component', function ($originalContent, $expectedContent) {
    $replacer = new IconReplacer;
    $iconsMap = ['old-icon-name' => 'new-icon-name'];

    $replacedContent = $replacer->withIconMap($iconsMap)->replaceIcons($originalContent);

    expect($replacedContent->new)->toBe($expectedContent)
        ->and($replacedContent->count())->toBe(1);
})->with('BladeComponents');

// Test for SVG directive syntax
it('replaces heroicons correctly when used as @svg directive', function ($originalContent, $expectedContent) {
    $replacer = new IconReplacer;
    $iconsMap = ['old-icon-name' => 'new-icon-name'];

    $replacedContent = $replacer->withIconMap($iconsMap)->replaceIcons($originalContent);

    expect($replacedContent->new)->toBe($expectedContent)
        ->and($replacedContent->count())->toBe(1);
})->with('SvgDirectives');

// Test for quoted string syntax
it('replaces heroicons correctly when used inside a quoted string', function ($originalContent, $expectedContent) {
    $replacer = new IconReplacer;
    $iconsMap = ['old-icon-name' => 'new-icon-name'];

    $replacedContent = $replacer->withIconMap($iconsMap)->replaceIcons($originalContent);

    expect($replacedContent->new)->toBe($expectedContent)
        ->and($replacedContent->count())->toBe(1);
})->with('Strings');

it('quick test', function () {
    $replacer = new IconReplacer;
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
    $replacer = new IconReplacer;
    $iconsMap = ['old-icon-name' => 'new-icon-name'];

    $originalContent = ' Text with "\'heroicon-o-old-icon-name" inside ';
    $expectedContent = ' Text with "\'heroicon-o-new-icon-name" inside ';

    $replacedContent = $replacer->withIconMap($iconsMap)->replaceIcons($originalContent);

    expect($replacedContent->new)->toBe($expectedContent)
        ->and($replacedContent->count())->toBe(1);
});

it('it replaces 2', function () {
    $replacer = new IconReplacer;
    $iconsMap = ['old-icon-name' => 'new-icon-name'];

    $originalContent = ' Text with "\'heroicon-o-old-icon-name" inside ';
    $expectedContent = ' Text with "\'heroicon-o-new-icon-name" inside ';

    $replacedContent = $replacer->withIconMap($iconsMap)->replaceIcons($originalContent);

    expect($replacedContent->new)->toBe($expectedContent)
        ->and($replacedContent->count())->toBe(1);
});

it('it replaces multiple variants', function () {
    $replacer = new IconReplacer;
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
    $replacer = new IconReplacer;
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
    $replacer = new IconReplacer;
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

// Tests using new datasets
it('replaces heroicons correctly in complex Blade scenarios', function ($originalContent, $expectedContent) {
    $replacer = new IconReplacer;
    $iconsMap = ['old-icon-name' => 'new-icon-name'];

    $replacedContent = $replacer->withIconMap($iconsMap)->replaceIcons($originalContent);

    expect($replacedContent->new)->toBe($expectedContent);
})->with('ComplexBlade');

it('replaces heroicons correctly inside Blade control structures', function ($originalContent, $expectedContent) {
    $replacer = new IconReplacer;
    $iconsMap = ['old-icon-name' => 'new-icon-name'];

    $replacedContent = $replacer->withIconMap($iconsMap)->replaceIcons($originalContent);

    expect($replacedContent->new)->toBe($expectedContent);
})->with('ControlStructures');

it('replaces heroicons correctly in Alpine.js and Livewire contexts', function ($originalContent, $expectedContent) {
    $replacer = new IconReplacer;
    $iconsMap = ['old-icon-name' => 'new-icon-name'];

    $replacedContent = $replacer->withIconMap($iconsMap)->replaceIcons($originalContent);

    expect($replacedContent->new)->toBe($expectedContent);
})->with('AlpineAndLivewire');

// Edge cases
it('handles empty content', function () {
    $replacer = new IconReplacer(['archive' => 'archive-box']);

    $result = $replacer->replaceIcons('');

    expect($result->new)->toBe('')
        ->and($result->old)->toBe('')
        ->and($result->count())->toBe(0)
        ->and($result->isEmpty())->toBeTrue();
});

it('handles whitespace-only content', function () {
    $replacer = new IconReplacer(['archive' => 'archive-box']);

    $result = $replacer->replaceIcons("   \n\t  \n  ");

    expect($result->new)->toBe("   \n\t  \n  ")
        ->and($result->count())->toBe(0);
});

it('handles content with no icon matches', function () {
    $replacer = new IconReplacer(['archive' => 'archive-box']);

    $content = '<div class="container"><p>Hello World</p></div>';
    $result = $replacer->replaceIcons($content);

    expect($result->new)->toBe($content)
        ->and($result->count())->toBe(0)
        ->and($result->isEmpty())->toBeTrue();
});

it('correctly handles icons at start of content', function () {
    $replacer = new IconReplacer(['archive' => 'archive-box']);

    $content = '<x-heroicon-o-archive /> followed by text';
    $result = $replacer->replaceIcons($content);

    expect($result->new)->toBe('<x-heroicon-o-archive-box /> followed by text')
        ->and($result->count())->toBe(1);
});

it('correctly handles icons at end of content', function () {
    $replacer = new IconReplacer(['archive' => 'archive-box']);

    $content = 'Text before <x-heroicon-o-archive />';
    $result = $replacer->replaceIcons($content);

    expect($result->new)->toBe('Text before <x-heroicon-o-archive-box />')
        ->and($result->count())->toBe(1);
});

it('handles mixed line endings (CRLF and LF)', function () {
    $replacer = new IconReplacer(['archive' => 'archive-box']);

    // Mix of \r\n (Windows) and \n (Unix) line endings
    $content = "<x-heroicon-o-archive />\r\n<x-heroicon-o-archive />\n<x-heroicon-o-archive />";
    $result = $replacer->replaceIcons($content);

    expect($result->new)->toBe("<x-heroicon-o-archive-box />\r\n<x-heroicon-o-archive-box />\n<x-heroicon-o-archive-box />")
        ->and($result->count())->toBe(3);
});

it('does not match partial icon names', function () {
    $replacer = new IconReplacer(['server' => 'server-stack']);

    // "server-stack" should not match the "server" pattern
    $content = '<x-heroicon-o-server-stack /> and <x-heroicon-o-server />';
    $result = $replacer->replaceIcons($content);

    // Only the standalone "server" should be replaced
    expect($result->new)->toBe('<x-heroicon-o-server-stack /> and <x-heroicon-o-server-stack />')
        ->and($result->count())->toBe(1);
});

it('handles icons with hyphens in the middle of names correctly', function () {
    $replacer = new IconReplacer([
        'arrow-sm-up' => 'arrow-small-up',
        'arrow-narrow-down' => 'arrow-long-down',
    ]);

    $content = '<x-heroicon-o-arrow-sm-up /> <x-heroicon-o-arrow-narrow-down />';
    $result = $replacer->replaceIcons($content);

    expect($result->new)->toBe('<x-heroicon-o-arrow-small-up /> <x-heroicon-o-arrow-long-down />')
        ->and($result->count())->toBe(2);
});

it('handles icons in HTML comments', function () {
    $replacer = new IconReplacer(['archive' => 'archive-box']);

    // Icons in comments should still be replaced
    $content = '<!-- <x-heroicon-o-archive /> -->';
    $result = $replacer->replaceIcons($content);

    expect($result->new)->toBe('<!-- <x-heroicon-o-archive-box /> -->')
        ->and($result->count())->toBe(1);
});

it('handles icons in Blade comments', function () {
    $replacer = new IconReplacer(['archive' => 'archive-box']);

    // Icons in Blade comments should still be replaced
    $content = '{{-- <x-heroicon-o-archive /> --}}';
    $result = $replacer->replaceIcons($content);

    expect($result->new)->toBe('{{-- <x-heroicon-o-archive-box /> --}}')
        ->and($result->count())->toBe(1);
});

it('correctly calculates line and column positions for replacements', function () {
    $replacer = new IconReplacer(['archive' => 'archive-box']);

    $content = "Line 1\nLine 2 <x-heroicon-o-archive />\nLine 3";
    $result = $replacer->inFile('/test.blade.php')->replaceIcons($content);

    expect($result->count())->toBe(1)
        ->and($result->replacements[0]->line)->toBe(2)
        ->and($result->replacements[0]->column)->toBeGreaterThan(0);
});

it('handles very long lines without issues', function () {
    $replacer = new IconReplacer(['archive' => 'archive-box']);

    $longLine = str_repeat('x', 5000).' <x-heroicon-o-archive /> '.str_repeat('y', 5000);
    $result = $replacer->replaceIcons($longLine);

    expect($result->count())->toBe(1)
        ->and($result->new)->toContain('<x-heroicon-o-archive-box />');
});

it('handles multiple adjacent icons without separators', function () {
    $replacer = new IconReplacer(['archive' => 'archive-box', 'bell' => 'bell']);

    $content = '<x-heroicon-o-archive/><x-heroicon-o-archive/><x-heroicon-o-archive/>';
    $result = $replacer->replaceIcons($content);

    expect($result->new)->toBe('<x-heroicon-o-archive-box/><x-heroicon-o-archive-box/><x-heroicon-o-archive-box/>')
        ->and($result->count())->toBe(3);
});

it('handles icons with all three variants in same content', function () {
    $replacer = new IconReplacer(['archive' => 'archive-box']);

    $content = '<x-heroicon-o-archive /> <x-heroicon-s-archive /> <x-heroicon-m-archive />';
    $result = $replacer->replaceIcons($content);

    expect($result->new)->toBe('<x-heroicon-o-archive-box /> <x-heroicon-s-archive-box /> <x-heroicon-m-archive-box />')
        ->and($result->count())->toBe(3);
});

it('inFile method sets file path correctly', function () {
    $replacer = new IconReplacer(['archive' => 'archive-box']);

    $content = '<x-heroicon-o-archive />';
    $filePath = '/path/to/test.blade.php';

    $result = $replacer->inFile($filePath)->replaceIcons($content);

    expect($result->replacements[0]->filePath)->toBe($filePath);
});

it('handles unicode characters in content around icons', function () {
    $replacer = new IconReplacer(['archive' => 'archive-box']);

    $content = '🎉 <x-heroicon-o-archive /> 你好';
    $result = $replacer->replaceIcons($content);

    expect($result->new)->toBe('🎉 <x-heroicon-o-archive-box /> 你好')
        ->and($result->count())->toBe(1);
});
