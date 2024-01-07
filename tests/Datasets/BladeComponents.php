<?php

dataset('BladeComponents', [
    ['<x-heroicon-o-old-icon-name>', '<x-heroicon-o-new-icon-name>'],
    [' <x-heroicon-o-old-icon-name> ', ' <x-heroicon-o-new-icon-name> '],
    ['<x-heroicon-o-old-icon-name/>', '<x-heroicon-o-new-icon-name/>'],
    ['<x-heroicon-o-old-icon-name />', '<x-heroicon-o-new-icon-name />'],
    ['<x-heroicon-o-old-icon-name \n/>', '<x-heroicon-o-new-icon-name \n/>'],
    ["<x-heroicon-o-old-icon-name\n/>", "<x-heroicon-o-new-icon-name\n/>"],
    ["<x-heroicon-o-old-icon-name\t\n/>", "<x-heroicon-o-new-icon-name\t\n/>"],
    ["<x-heroicon-o-old-icon-name\n\t\n/>", "<x-heroicon-o-new-icon-name\n\t\n/>"],
    ["<x-heroicon-m-old-icon-name\n\t\n/>", "<x-heroicon-m-new-icon-name\n\t\n/>"],
    ["\n <x-heroicon-o-old-icon-name> \n", "\n <x-heroicon-o-new-icon-name> \n"],
    ["\n<x-heroicon-o-old-icon-name> \n", "\n<x-heroicon-o-new-icon-name> \n"],
    ["\n<x-heroicon-o-old-icon-name\n> \n", "\n<x-heroicon-o-new-icon-name\n> \n"],
    [' Some text before <x-heroicon-o-old-icon-name> and after', ' Some text before <x-heroicon-o-new-icon-name> and after'],
]);
