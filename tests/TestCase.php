<?php

namespace HelgeSverre\BladeHeroiconsUpgrader\Tests;

use HelgeSverre\BladeHeroiconsUpgrader\BladeHeroiconsUpgraderServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            BladeHeroiconsUpgraderServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {

    }
}
