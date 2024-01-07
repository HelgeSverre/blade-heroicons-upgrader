<?php

namespace HelgeSverre\BladeHeroiconsUpgrader;

use HelgeSverre\BladeHeroiconsUpgrader\Data\Replacement;
use HelgeSverre\BladeHeroiconsUpgrader\Data\Result;
use Spatie\Regex\MatchResult;
use Spatie\Regex\Regex;

class IconReplacer
{
    protected ?string $filePath = null;

    public static array $variants = ['o', 's', 'm'];

    public function __construct(protected array $iconMap = [])
    {

    }

    public function inFile($sampleFile): static
    {
        $this->filePath = $sampleFile;

        return $this;
    }

    public function withIconMap(array $iconsMap): static
    {
        $this->iconMap = $iconsMap;

        return $this;
    }

    public function replaceIcons(string $contents): Result
    {
        $newContents = $contents;
        $replacements = [];

        foreach ($this->iconMap as $oldName => $newName) {
            if ($oldName === $newName) {
                continue;
            }

            foreach (self::$variants as $variant) {

                $pattern = implode('', [
                    '#(?<=\s|\'|"|<|\/)',   // Lookbehind for separators
                    '(?<icon>',             // Start named group 'icon'
                    '(?<prefix>x-)?',       // Named group 'prefix' for optional 'x-'
                    'heroicon-',            // Literal string 'heroicon-'
                    $variant,               // Variant part of the icon name
                    '-',                    // Literal hyphen
                    $oldName,               // Old name of the icon
                    ')',                    // End named group 'icon'
                    '(?=\s|\'|"|>|\/)#m',    // Lookahead for separators and end of pattern with multiline flag
                ]);

                $positions = $this->findMatchPositions($pattern, $newContents);

                foreach ($positions as $position) {

                    // Add to replacements array for tracking
                    $replacements[] = new Replacement(
                        oldIcon: "heroicon-{$variant}-{$oldName}",
                        newIcon: "heroicon-{$variant}-{$newName}",
                        pattern: $pattern,
                        line: $position['line'],
                        column: $position['column'],
                        filePath: $this->filePath,
                    );
                }
            }
        }

        foreach ($replacements as $replacement) {
            $newContents = Regex::replace(
                pattern: $replacement->pattern,
                replacement: fn (MatchResult $result) => $result->groupOr('prefix', '').$replacement->newIcon,
                subject: $newContents
            )->result();
        }

        $adjustedReplacements = array_reduce($replacements, function ($carry, $replacement) {
            $newPosition = $replacement->column + strlen($replacement->newIcon) - strlen($replacement->oldIcon);
            $carry[] = new Replacement(
                oldIcon: $replacement->oldIcon,
                newIcon: $replacement->newIcon,
                pattern: $replacement->pattern,
                line: $replacement->line,
                column: $newPosition,
                filePath: $replacement->filePath
            );

            return $carry;
        }, []);

        return new Result(
            new: $newContents,
            old: $contents,
            replacements: $replacements,
            adjustedReplacements: $adjustedReplacements,
        );
    }

    public function findMatchPositions(string $pattern, string $contents): array
    {
        $positions = [];

        if (preg_match_all($pattern, $contents, $matches, PREG_OFFSET_CAPTURE)) {

            foreach ($matches['icon'] as $match) {
                $offsetIntoContent = $match[1];

                // Find the last newline character before the match
                $lastNewLinePos = strrpos(substr($contents, 0, $offsetIntoContent), "\n");

                // Column is the distance from the last newline to the match start
                // If there's no newline, it starts from the beginning of the contents
                $column = $lastNewLinePos !== false ?
                    $offsetIntoContent - $lastNewLinePos - 1 :
                    $offsetIntoContent;

                $line = substr_count(substr($contents, 0, $offsetIntoContent), "\n") + 1;

                $positions[] = [
                    'line' => $line,
                    'column' => $column,
                    'icon' => $match[0],
                ];
            }
        }

        return $positions;
    }
}
