<?php

namespace HelgeSverre\BladeHeroiconsUpgrader;

use HelgeSverre\BladeHeroiconsUpgrader\Data\Replacement;
use HelgeSverre\BladeHeroiconsUpgrader\Data\Result;
use Spatie\Regex\Regex;

class IconReplacer
{
    public static array $variants = ['o', 's', 'm'];

    public function replaceIcons(string $contents, array $iconsMap): Result
    {
        $newContents = $contents;
        $replacements = [];

        foreach ($iconsMap as $oldName => $newName) {
            if ($oldName === $newName) {
                continue;
            }

            foreach (self::$variants as $variant) {
                $pattern = "#(<x-)?(x-heroicon-|heroicon-){$variant}-".preg_quote($oldName, '#')."\b#";

                $matches = Regex::matchAll($pattern, $newContents)->results();
                foreach ($matches as $match) {
                    $prefix = $match->groupOr(1, '') ?? ''; // Capture the '<x-' prefix if it's present
                    $replaceWith = "{$prefix}heroicon-{$variant}-{$newName}";

                    // Add to replacements array for tracking
                    $replacements[] = new Replacement(
                        oldIcon: "heroicon-{$variant}-{$oldName}",
                        newIcon: "heroicon-{$variant}-{$newName}",
                        variant: $variant,
                        line: 0, // You might want to capture the actual line and column if possible
                        column: 0,
                    );

                    // Perform replacement
                    $newContents = str_replace($match->groupOr(0, ''), $replaceWith, $newContents);
                }
            }
        }

        return new Result(
            new: $newContents,
            old: $contents,
            replacements: $replacements
        );
    }
}
