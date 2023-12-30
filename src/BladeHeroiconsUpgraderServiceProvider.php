<?php

namespace HelgeSverre\BladeHeroiconsUpgrader;

use HelgeSverre\BladeHeroiconsUpgrader\Commands\UpgradeIcons;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class BladeHeroiconsUpgraderServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('blade-heroicons-upgrader')
            ->hasConfigFile()
            ->hasCommand(UpgradeIcons::class);
    }
}
