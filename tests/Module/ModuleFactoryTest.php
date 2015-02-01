<?php
namespace Puppy\Module;

use Puppy\Application;
use Puppy\Config\Config;
use Puppy\Config\SimpleConfig;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ModuleFactoryTest
 * @package Puppy\Module
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class ModuleFactoryTest extends \PHPUnit_Framework_TestCase
{

    public function testCreateFromApplication()
    {
        $factory = new ModuleFactory();
        $result = $factory->createFromApplication(new Application(new Config(), new Request()));
        $this->assertInstanceOf('Puppy\Module\ModulesLoader', $result);
    }

    public function testCreateFromApplicationWithProxienable()
    {
        $config = [
            'module.cache.enable' => true,
            'module.cache.path' => __DIR__,
            'module.directories' => [__DIR__]
        ];

        $factory = new ModuleFactory();
        $result = $factory->createFromApplication(new Application(new SimpleConfig($config), new Request()));
        $this->assertInstanceOf('Puppy\Module\ModulesLoaderProxy', $result);
    }

    public function testCreateFromApplicationWithProxiDisable()
    {
        $config = [
            'module.cache.enable' => false,
            'module.cache.path' => __DIR__,
            'module.directories' => [__DIR__]
        ];

        $factory = new ModuleFactory();
        $result = $factory->createFromApplication(new Application(new SimpleConfig($config), new Request()));
        $this->assertInstanceOf('Puppy\Module\ModulesLoader', $result);
    }
}
 