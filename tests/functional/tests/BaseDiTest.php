<?php

namespace Butterfly\Tests;

use Butterfly\Component\DI\Container;
use Butterfly\Component\Packages\ExtendedDiConfig;

abstract class BaseDiTest extends \PHPUnit_Framework_TestCase
{
    protected static $baseDir;
    protected static $configPath;

    /**
     * @var Container
     */
    protected static $container;

    public static function setUpBeforeClass()
    {
        self::$baseDir    = realpath(__DIR__ . '/..');
        self::$configPath = self::$baseDir . '/var/config.php';

        ExtendedDiConfig::buildForComposer(self::$baseDir, self::$configPath, static::getAdditionalConfigPaths());

        self::$container = new Container(require self::$configPath);
    }

    /**
     * @return array
     */
    protected static function getAdditionalConfigPaths()
    {
        return array();
    }

    public static function tearDownAfterClass()
    {
        unlink(self::$configPath);
    }
}
