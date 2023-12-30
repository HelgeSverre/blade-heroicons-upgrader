<?php

namespace HelgeSverre\BladeHeroiconsUpgrader\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Spatie\Regex\Regex;

class UpgradeIcons extends Command
{
    protected $signature = 'blade-heroicons-upgrader:upgrade
                            {path=./resources/views : The path to the directory or file to replace icons in}
                            {--dry : Perform a dry run without actually replacing any icons}';

    protected $description = 'Replace old icon names with new ones';

    public static array $variants = ['o', 's', 'm'];

    public function handle()
    {

        $path = $this->argument('path');

        if (! File::exists($path)) {
            $this->error("The path {$path} does not exist.");

            return;
        }

        $files = File::allFiles($path);

        $iconsMap = $this->getIconsMap();
        $totalReplaced = 0;

        foreach ($files as $file) {
            $this->info("\n{$file->getRelativePath()}");

            $count = $this->replaceIconsInFile($file, $iconsMap);

            $totalReplaced += $count;

            $this->info("> Replaced {$count} icon names in file.");
        }

        $this->info(">>> Replaced {$totalReplaced} icon names in total.");
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
