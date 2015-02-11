<?php

namespace Butterfly\Tests;

use Butterfly\Component\Packages\PackagesConfig;
use Butterfly\Component\DI\Container;

abstract class BaseDiTest extends \PHPUnit_Framework_TestCase
{
    protected static $baseDir;

    /**
     * @var Container
     */
    protected static $container;

    public static function setUpBeforeClass()
    {
        self::$baseDir = realpath(__DIR__ . '/..');

        $config = PackagesConfig::buildForComposer(self::$baseDir, static::getAdditionalConfigPaths());

        self::$container = new Container($config);
    }

    /**
     * @return array
     */
    protected static function getAdditionalConfigPaths()
    {
        return array();
    }
}
