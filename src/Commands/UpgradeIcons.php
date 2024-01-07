<?php

namespace HelgeSverre\BladeHeroiconsUpgrader\Commands;

use HelgeSverre\BladeHeroiconsUpgrader\Data\Replacement;
use HelgeSverre\BladeHeroiconsUpgrader\IconReplacer;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Laravel\Prompts\Progress;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\note;
use function Laravel\Prompts\progress;
use function Laravel\Prompts\table;

class UpgradeIcons extends Command
{
    protected $signature = 'blade-heroicons-upgrader:upgrade {paths?*} {--dry : Perform a dry run without actually replacing any icons} ';

    protected $description = 'Replace references to Heroicons v1 icon names with Heroicon v2 icon names';

    public function handle()
    {
        $start = hrtime(true);
        $paths = Arr::wrap($this->argument('paths'));

        if (empty($paths)) {
            $paths = ['./resources/views'];
            $confirmed = confirm(
                label: 'No path(s) provided, use defaults?',
                hint: "Default path: {$paths[0]}"
            );

            if (! $confirmed) {
                error('Aborting');

                return;
            }

            info("Using default path: {$paths[0]}");

        }

        /**
         * @var Replacement[] $replacements
         */
        $replacements = [];

        $iconReplacer = new IconReplacer(
            config('blade-heroicons-upgrader.replacements')
        );

        $allFiles = collect($paths)->flatMap(function ($path) {
            $realPath = realpath($path);
            if (File::missing($realPath)) {
                return null;
            }

            return File::isFile($realPath) ? [$realPath] : File::allFiles($realPath);
        })->filter()->values();

        progress(
            label: 'Processing files',
            steps: $allFiles,
            callback: function (string $file, Progress $progress) use ($iconReplacer, &$replacements) {

                $progress->hint("Processing: $file");

                $contents = File::get($file);

                $info = $iconReplacer->inFile($file)->replaceIcons($contents);

                if (! $this->option('dry')) {
                    File::put($file, $info->new);

                    foreach ($info->adjustedReplacements as $replacement) {
                        $replacements[] = $replacement;
                    }
                }

                foreach ($info->replacements as $replacement) {
                    $replacements[] = $replacement;
                }
            },
        );

        table(
            ['Old icon', 'New Icon', 'Location'],
            array_map(function ($replacement) {
                $relativePath = str_replace(getcwd().'/', '', $replacement->filePath);

                return [
                    $replacement->oldIcon,
                    $replacement->newIcon,
                    "{$relativePath}:{$replacement->line}:{$replacement->column}",
                ];
            }, $replacements)
        );

        $replacementCount = count($replacements);

        $timeInSec = round((hrtime(true) - $start) / 1000000000, 2);

        info("Replaced {$replacementCount} icons across {$allFiles->count()} files.");
        note("Took {$timeInSec} seconds");
    }
}
