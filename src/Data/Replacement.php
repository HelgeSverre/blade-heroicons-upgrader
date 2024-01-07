<?php

namespace HelgeSverre\BladeHeroiconsUpgrader\Data;

class Replacement
{
    public function __construct(
        public readonly string $oldIcon,
        public readonly string $newIcon,
        public readonly string $pattern,
        public readonly int $line,
        public readonly int $column,
        public readonly ?string $filePath,
    ) {

    }
}
