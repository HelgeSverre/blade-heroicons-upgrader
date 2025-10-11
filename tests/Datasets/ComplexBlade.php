<?php

// Mixed syntax: Blade components, @svg directives, and strings all in the same content
dataset('ComplexBlade', [
    // Mix of component and @svg
    [
        '<x-heroicon-o-old-icon-name /> @svg("heroicon-o-old-icon-name")',
        '<x-heroicon-o-new-icon-name /> @svg("heroicon-o-new-icon-name")',
    ],
    // Component, @svg, and string
    [
        '<div><x-heroicon-o-old-icon-name /> @svg(\'heroicon-o-old-icon-name\') $icon = "heroicon-o-old-icon-name";</div>',
        '<div><x-heroicon-o-new-icon-name /> @svg(\'heroicon-o-new-icon-name\') $icon = "heroicon-o-new-icon-name";</div>',
    ],
    // Multiple components on same line
    [
        '<x-heroicon-o-old-icon-name /> <x-heroicon-s-old-icon-name /> <x-heroicon-m-old-icon-name />',
        '<x-heroicon-o-new-icon-name /> <x-heroicon-s-new-icon-name /> <x-heroicon-m-new-icon-name />',
    ],
    // Multiple @svg directives on same line
    [
        '@svg("heroicon-o-old-icon-name") @svg("heroicon-s-old-icon-name")',
        '@svg("heroicon-o-new-icon-name") @svg("heroicon-s-new-icon-name")',
    ],
    // Nested in HTML attributes
    [
        '<button class="btn" data-icon="heroicon-o-old-icon-name">Text</button>',
        '<button class="btn" data-icon="heroicon-o-new-icon-name">Text</button>',
    ],
    // In Blade echo syntax
    [
        '{{ $icon = "heroicon-o-old-icon-name" }}',
        '{{ $icon = "heroicon-o-new-icon-name" }}',
    ],
    // Adjacent components without space
    [
        '<x-heroicon-o-old-icon-name/><x-heroicon-o-old-icon-name/>',
        '<x-heroicon-o-new-icon-name/><x-heroicon-o-new-icon-name/>',
    ],
    // Mixed quotes
    [
        '@svg("heroicon-o-old-icon-name") . @svg(\'heroicon-s-old-icon-name\')',
        '@svg("heroicon-o-new-icon-name") . @svg(\'heroicon-s-new-icon-name\')',
    ],
    // In array/object notation
    [
        "['icon' => 'heroicon-o-old-icon-name']",
        "['icon' => 'heroicon-o-new-icon-name']",
    ],
    // Multiline component with other content
    [
        "<div>\n    <x-heroicon-o-old-icon-name />\n    @svg('heroicon-o-old-icon-name')\n</div>",
        "<div>\n    <x-heroicon-o-new-icon-name />\n    @svg('heroicon-o-new-icon-name')\n</div>",
    ],
]);
