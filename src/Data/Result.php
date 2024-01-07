<?php

namespace HelgeSverre\BladeHeroiconsUpgrader\Data;

class Result
{
    /**
     * @param  array|Replacement[]  $replacements
     */
    public function __construct(
        public readonly string $new,
        public readonly string $old,
        public readonly array $replacements,
        public readonly array $adjustedReplacements,

    ) {

    }

    public function isEmpty(): bool
    {
        return empty($this->replacements);
    }

    public function isNotEmpty(): bool
    {
        return ! $this->isEmpty();
    }

    public function count(): int
    {
        return count($this->replacements);
    }
}
