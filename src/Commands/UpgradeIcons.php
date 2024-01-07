<?php

namespace HelgeSverre\BladeHeroiconsUpgrader\Commands;

use HelgeSverre\BladeHeroiconsUpgrader\IconReplacer;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;

use function config;

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

        $iconReplacer = new IconReplacer(
            config('blade-heroicons-upgrader.replacements')
        );

        foreach ($paths as $path) {
            $realPath = realpath($path);

            if (! File::exists($realPath)) {
                $this->error("The path {$path} does not exist.");

                return;
            }

            $files = File::isFile($realPath) ? [$realPath] : File::allFiles($realPath);

            foreach ($files as $file) {
                $this->info("{$file}");

                $contents = File::get($file);

                $info = $iconReplacer->inFile($file)->replaceIcons($contents);

                if (! $this->option('dry')) {
                    File::put($file, $info->new);
                }

                $totalReplaced += $info->count();

                if ($info->isEmpty()) {
                    $totalFilesWithReplacements++;
                    $this->comment("> Replaced {$info->count()} icons.\n");
                }

            }

        }

        $this->comment("\n\nDONE: Replaced {$totalReplaced} icons across {$totalFilesWithReplacements} files.");
    }
}
