<?php

namespace HelgeSverre\BladeHeroiconsUpgrader;

use HelgeSverre\BladeHeroiconsUpgrader\Data\Replacement;
use HelgeSverre\BladeHeroiconsUpgrader\Data\Result;
use Spatie\Regex\MatchResult;
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

                $pattern = '#(?<=\s|\'|"|<|\/)(?<icon>(?<prefix>x-)?heroicon-' . $variant . '-' . $oldName . ')(?=\s|\'|"|>|\/)#m';

                $positions = $this->findMatchPositions($pattern, $newContents);

                $result = Regex::matchAll($pattern, $newContents);

                if (!$result->hasMatch()) {
                    continue;
                }

                foreach ($result->results() as $match) {


                    dd($match);

                    // Add to replacements array for tracking
                    $replacements[] = new Replacement(
                        oldIcon: "heroicon-{$variant}-{$oldName}",
                        newIcon: "heroicon-{$variant}-{$newName}",
                        line: 0, // You might want to capture the actual line and column if possible
                        column: 0,
                    );

                    $result = Regex::replace($pattern, function (MatchResult $result) use ($newName, $variant) {

                        $prefix = $result->groupOr('prefix', '');
                        $replacement = $prefix . "heroicon-{$variant}-{$newName}";

                        return str_replace($result->group('icon'), $replacement, $result->result());

                    }, $newContents);

                    $newContents = $result->result();

                }
            }
        }

        return new Result(
            new: $newContents,
            old: $contents,
            replacements: $replacements
        );
    }

    public function findMatchPositions(string $pattern, string $contents): array
    {
        $positions = [];

        if (preg_match_all($pattern, $contents, $matches, PREG_OFFSET_CAPTURE)) {
            foreach ($matches["icon"] as $match) {
                $offsetIntoContent = $match[1];

                $column = strlen(substr($contents, 0, $offsetIntoContent)) - strlen(str_replace("\n", '', substr($contents, 0, $offsetIntoContent)));
                $line = substr_count(substr($contents, 0, $offsetIntoContent), "\n") + 1;

            }
        }

        return $positions;
    }
}
