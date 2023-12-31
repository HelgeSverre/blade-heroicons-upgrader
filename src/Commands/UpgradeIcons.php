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
                // Existing pattern for heroicon
                $boundary = "(?<=\s|'|\"|/|^)";
                $endBoundary = "(?=\s|'|\"|/|$)";
                $pattern = '#'.$boundary.'heroicon-'.$variant.'-'.preg_quote($oldName, '#').$endBoundary.'#';
                $contents = $this->replacePattern($contents, $pattern, "heroicon-{$variant}-{$newName}", $replaced);

                // New pattern for Blade component syntax
                $bladePattern = "#<x-heroicon-{$variant}-".preg_quote($oldName, '#')."([\\s>])#";
                $contents = $this->replacePattern($contents, $bladePattern, "<x-heroicon-{$variant}-{$newName}$1", $replaced);
            }
        }

        if ($this->option('dry')) {
            return $replaced;
        }

        file_put_contents($file, $contents);

        return $replaced;
    }

    protected function replacePattern(string $contents, string $pattern, string $replacement, int &$replacedCount): string
    {
        $matched = Regex::matchAll($pattern, $contents)->results();
        $replacedCount += count($matched);

        return Regex::replace($pattern, $replacement, $contents)->result();
    }

    protected function getIconsMap(): array
    {
        return config('blade-heroicons-upgrader.replacements');
    }
}
