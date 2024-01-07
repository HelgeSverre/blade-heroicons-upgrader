<?php

dataset('SvgDirectives', [
    ["@svg('heroicon-o-old-icon-name', 'class-name')", "@svg('heroicon-o-new-icon-name', 'class-name')"],
    [" \n @svg('heroicon-o-old-icon-name', 'class-name') \n ", " \n @svg('heroicon-o-new-icon-name', 'class-name') \n "],
]);
