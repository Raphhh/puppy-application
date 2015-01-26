<?php
namespace Puppy\Module;

use Puppy\resources\src\ModuleMock1;
use Puppy\resources\src\ModuleMock2;
use Stash\Driver\FileSystem;
use Stash\Pool;

/**
 * Class ModulesLoaderProxyTest
 * @package Puppy\Module
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class ModulesLoaderProxyTest extends \PHPUnit_Framework_TestCase
{

    public function testGetModules()
    {
        $modules = [
            new ModuleMock1(),
            new ModuleMock2()
        ];

        $modulesLoader = $this->provideModulesLoader($modules);

        $driver = new FileSystem();
        $driver->setOptions(['path' => __DIR__.'/stash']);
        $driver->clear();

        $proxy = new ModulesLoaderProxy($modulesLoader, new Pool($driver));
        $this->assertEquals($modules, $proxy->getModules());

        $proxy = new ModulesLoaderProxy($modulesLoader, new Pool($driver));
        $this->assertEquals($modules, $proxy->getModules());

        $driver->clear();
    }

    /**
     * @param IModule[] $modules
     * @return \Puppy\Module\IModulesLoader
     */
    private function provideModulesLoader(array $modules)
    {
        $modulesLoader = $this->getMockBuilder('Puppy\Module\ModulesLoader')->getMock();

        $modulesLoader->expects($this->once())
            ->method('getModules')
            ->will($this->returnValue($modules));

        return $modulesLoader;
    }


}
 