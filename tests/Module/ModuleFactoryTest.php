<?php
namespace Puppy\Module;

use Puppy\Application;
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
        $result = $factory->createFromApplication(new Application(new \ArrayObject(), new Request()));
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
        $result = $factory->createFromApplication(new Application(new \ArrayObject($config), new Request()));
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
        $result = $factory->createFromApplication(new Application(new \ArrayObject($config), new Request()));
        $this->assertInstanceOf('Puppy\Module\ModulesLoader', $result);
    }
}
 