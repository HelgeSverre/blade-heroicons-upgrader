<?php

namespace HelgeSverre\BladeHeroiconsUpgrader;

use HelgeSverre\BladeHeroiconsUpgrader\Commands\UpgradeIcons;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class BladeHeroiconsUpgraderServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('blade-heroicons-upgrader')
            ->hasCommand(UpgradeIcons::class);
    }
}
