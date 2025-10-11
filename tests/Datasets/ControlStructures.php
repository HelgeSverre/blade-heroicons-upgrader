<?php

// Icons inside Blade control structures
dataset('ControlStructures', [
    // Inside @if
    [
        '@if($show) <x-heroicon-o-old-icon-name /> @endif',
        '@if($show) <x-heroicon-o-new-icon-name /> @endif',
    ],
    // Inside @foreach
    [
        "@foreach(\$items as \$item)\n    <x-heroicon-o-old-icon-name />\n@endforeach",
        "@foreach(\$items as \$item)\n    <x-heroicon-o-new-icon-name />\n@endforeach",
    ],
    // Inside @for
    [
        "@for(\$i = 0; \$i < 5; \$i++)\n    @svg('heroicon-o-old-icon-name')\n@endfor",
        "@for(\$i = 0; \$i < 5; \$i++)\n    @svg('heroicon-o-new-icon-name')\n@endfor",
    ],
    // Inside @while
    [
        "@while(\$condition)\n    <x-heroicon-o-old-icon-name />\n@endwhile",
        "@while(\$condition)\n    <x-heroicon-o-new-icon-name />\n@endwhile",
    ],
    // Inside @switch
    [
        "@switch(\$type)\n    @case('icon')\n        <x-heroicon-o-old-icon-name />\n        @break\n@endswitch",
        "@switch(\$type)\n    @case('icon')\n        <x-heroicon-o-new-icon-name />\n        @break\n@endswitch",
    ],
    // Inside @unless
    [
        '@unless($hide) <x-heroicon-o-old-icon-name /> @endunless',
        '@unless($hide) <x-heroicon-o-new-icon-name /> @endunless',
    ],
    // Inside @isset
    [
        '@isset($icon) <x-heroicon-o-old-icon-name /> @endisset',
        '@isset($icon) <x-heroicon-o-new-icon-name /> @endisset',
    ],
    // Inside @empty
    [
        '@empty($items) <x-heroicon-o-old-icon-name /> @endempty',
        '@empty($items) <x-heroicon-o-new-icon-name /> @endempty',
    ],
    // Inside @auth
    [
        '@auth <x-heroicon-o-old-icon-name /> @endauth',
        '@auth <x-heroicon-o-new-icon-name /> @endauth',
    ],
    // Inside @guest
    [
        '@guest <x-heroicon-o-old-icon-name /> @endguest',
        '@guest <x-heroicon-o-new-icon-name /> @endguest',
    ],
    // Nested conditionals
    [
        "@if(\$a)\n    @if(\$b)\n        <x-heroicon-o-old-icon-name />\n    @endif\n@endif",
        "@if(\$a)\n    @if(\$b)\n        <x-heroicon-o-new-icon-name />\n    @endif\n@endif",
    ],
    // Inside @can
    [
        '@can("view", $post) <x-heroicon-o-old-icon-name /> @endcan',
        '@can("view", $post) <x-heroicon-o-new-icon-name /> @endcan',
    ],
    // Inside ternary in Blade echo
    [
        '{{ $show ? "heroicon-o-old-icon-name" : "heroicon-s-old-icon-name" }}',
        '{{ $show ? "heroicon-o-new-icon-name" : "heroicon-s-new-icon-name" }}',
    ],
    // Inside @forelse
    [
        "@forelse(\$items as \$item)\n    <x-heroicon-o-old-icon-name />\n@empty\n    No items\n@endforelse",
        "@forelse(\$items as \$item)\n    <x-heroicon-o-new-icon-name />\n@empty\n    No items\n@endforelse",
    ],
]);
