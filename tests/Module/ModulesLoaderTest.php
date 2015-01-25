<?php
namespace Puppy\Module;

/**
 * Class ModulesLoaderTest
 * @package Puppy\Module
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class ModulesLoaderTest extends \PHPUnit_Framework_TestCase
{
    private $cwd;

    public function setUp()
    {
        $this->cwd = getcwd();
        chdir(__DIR__ . '/../resources');
    }

    public function tearDown()
    {
        chdir($this->cwd);
    }

    public function testGetModules()
    {
        $modulesLoader = new ModulesLoader();
        $result = $modulesLoader->getModules();
        $this->assertCount(2, $result);
        $this->assertInstanceOf('Puppy\resources\src\ModuleMock1', $result[0]);
        $this->assertInstanceOf('Puppy\resources\src\ModuleMock2', $result[1]);
    }
}
 