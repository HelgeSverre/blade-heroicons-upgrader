<?php

namespace App\Commands;

use Exception;
use Illuminate\Support\Facades\File;
use LaravelZero\Framework\Commands\Command;
use Spatie\Regex\Regex;

class ReplaceIcons extends Command
{
    protected $signature = 'run
                            {path=./resources/views : The path to the directory or file to replace icons in.}
                            {--mapping=auto} : The name of the mapping file to use, leave blank to attempt autodetection
                            {--dry : Perform a dry run without actually replacing any icons.}';

    protected $description = 'Replace old icon names with new ones';

    public static array $variants = ['o', 's', 'm'];

    public function handle()
    {
        $files = File::allFiles(base_path('resources/views'));

        $files[] = base_path('app/Composers/MenuComposer.php');

        $iconsMap = $this->getIconsMap();
        $totalReplaced = 0;

        foreach ($files as $file) {
            $this->info("FILE: {$file}");

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
        return match ($this->argument('mapping')) {
            'auto' => $this->attemptAutodetectMapping() ?? throw new Exception('Autodetection failed'),
            'heroicons' => $this->loadMapping('heroicons'),
            default => throw new Exception("Unknown mapping file: {$this->argument('mapping')}"),
        };

    }

    protected function attemptAutodetectMapping()
    {
        // TODO: Grab the composer.json file to figure out which iconset is being used, if we dont have it, throw error.
    }

    private function loadMapping(string $mapping): array
    {
        $path = base_path("mappings/$mapping.json");

        if (! File::exists($path)) {
            throw new Exception("Mapping file does not exist: {$path}");
        }

        $json = File::get($path);

        return json_decode($json, true, 512, JSON_THROW_ON_ERROR);
    }
}
