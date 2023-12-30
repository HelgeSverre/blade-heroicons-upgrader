<?php

namespace HelgeSverre\BladeHeroiconsUpgrader\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Spatie\Regex\Regex;

class UpgradeIcons extends Command
{
    protected $signature = 'blade-heroicons-upgrader:upgrade {paths?*} {--dry : Perform a dry run without actually replacing any icons}';

    protected $description = 'Replace old icon names with new ones';

    public static array $variants = ['o', 's', 'm'];

    public function handle()
    {
        $paths = Arr::wrap($this->argument('paths'));

        if (empty($paths)) {
            $paths = ['./resources/views'];
            $this->comment("No path(s) provided, using default: {$paths[0]}");
        }

        $totalReplaced = 0;
        $totalFilesWithReplacements = 0;

        foreach ($paths as $path) {
            $realPath = realpath($path);

            if (! File::exists($realPath)) {
                $this->error("The path {$path} does not exist.");

                return;
            }

            $files = File::isFile($realPath) ? [$realPath] : File::allFiles($realPath);

            $iconsMap = $this->getIconsMap();

            foreach ($files as $file) {
                $this->info("{$file}");

                $count = $this->replaceIconsInFile($file, $iconsMap);

                $totalReplaced += $count;

                if ($count) {
                    $totalFilesWithReplacements++;
                    $this->comment("> Replaced {$count} icons.\n");
                }

            }

        }

        $this->comment("\n\nDONE: Replaced {$totalReplaced} icons across {$totalFilesWithReplacements} files.");
    }

    protected function replaceIconsInFile(string $file, array $iconsMap): int
    {
        $replaced = 0;
        $contents = file_get_contents($file);

        foreach ($iconsMap as $oldName => $newName) {
            if ($oldName === $newName) {
                continue;
            }

            foreach (self::$variants as $variant) {

                // Define a custom boundary for the regex.
                // This boundary ensures that the icon name is preceded and followed by specific characters (whitespace, quote, slash) or line boundaries.
                // Example:
                // - Matches: " heroicon-o-adjustments ", "'heroicon-s-adjustments'", "/heroicon-m-adjustments/"
                // - Does not match: "extra-heroicon-o-adjustments", "heroicon-o-adjustments-plus"
                $boundary = "(?<=\s|'|\"|/|^)";
                $endBoundary = "(?=\s|'|\"|/|$)";

                // Construct the regex pattern to match the old icon name with the variant and custom boundary
                $pattern = '#'.$boundary.'heroicon-'.$variant.'-'.preg_quote($oldName, '#').$endBoundary.'#';

                $matched = Regex::matchAll($pattern, $contents)->results();
                $replaced += count($matched);

                $replaceWith = "heroicon-{$variant}-{$newName}";

                $contents = Regex::replace($pattern, $replaceWith, $contents)->result();
            }
        }

        if ($this->option('dry')) {
            return $replaced;
        }

        file_put_contents($file, $contents);

        return $replaced;
    }

    protected function getIconsMap(): array
    {
        return config('blade-heroicons-upgrader.replacements');
    }
}
