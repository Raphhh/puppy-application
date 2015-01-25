<?php
namespace Puppy;

use Pimple\Container;
use Puppy\Module\ModulesLoader;
use Puppy\Route\Router;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ApplicationTest
 * @package Puppy
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class ApplicationTest extends \PHPUnit_Framework_TestCase
{
    private $cwd;

    public function setUp()
    {
        $this->cwd = getcwd();
        chdir(__DIR__ . '/resources');
    }

    public function tearDown()
    {
        chdir($this->cwd);
    }

    public function testRunWithoutController()
    {
        $application = new Application(new Request());
        $this->setExpectedException('DomainException', 'No route found for uri ""');
        $application->run();
    }

    public function testAny()
    {
        $controller = function(){};

        $application = new Application(new Request());
        $application->any('pattern', $controller);

        /**
         * @var Router $router
         */
        $router = $application->getServices()['router'];
        $route = $router->getRoutes()[0];
        $this->assertSame('pattern', $route->getPattern()->getUri());
        $this->assertSame('', $route->getPattern()->getMethod());
        $this->assertSame('', $route->getPattern()->getContentType());
    }

    public function testGet()
    {
        $controller = function(){};

        $application = new Application(new Request());
        $application->get('pattern', $controller);

        /**
         * @var Router $router
         */
        $router = $application->getServices()['router'];
        $route = $router->getRoutes()[0];
        $this->assertSame('pattern', $route->getPattern()->getUri());
        $this->assertSame('GET', $route->getPattern()->getMethod());
        $this->assertSame('', $route->getPattern()->getContentType());
    }

    public function testPost()
    {
        $controller = function(){};

        $application = new Application(new Request());
        $application->post('pattern', $controller);

        /**
         * @var Router $router
         */
        $router = $application->getServices()['router'];
        $route = $router->getRoutes()[0];
        $this->assertSame('pattern', $route->getPattern()->getUri());
        $this->assertSame('POST', $route->getPattern()->getMethod());
        $this->assertSame('', $route->getPattern()->getContentType());
    }

    public function testJson()
    {
        $controller = function(){};

        $application = new Application(new Request());
        $application->json('pattern', $controller);

        /**
         * @var Router $router
         */
        $router = $application->getServices()['router'];
        $route = $router->getRoutes()[0];
        $this->assertSame('pattern', $route->getPattern()->getUri());
        $this->assertSame('', $route->getPattern()->getMethod());
        $this->assertSame('application/json', $route->getPattern()->getContentType());
    }

    public function testInitModules()
    {
        $this->assertTrue(empty($GLOBALS['module_mock_init_1']));
        $this->assertTrue(empty($GLOBALS['module_mock_init_2']));

        $application = new Application(new Request());
        $application->initModules(new ModulesLoader());

        $this->assertFalse(empty($GLOBALS['module_mock_init_1']));
        $this->assertFalse(empty($GLOBALS['module_mock_init_2']));

        unset($GLOBALS['module_mock_init_1']);
        $this->assertTrue(empty($GLOBALS['module_mock_init_1']));

        unset($GLOBALS['module_mock_init_2']);
        $this->assertTrue(empty($GLOBALS['module_mock_init_2']));
    }

    public function testInitModulesWithoutExistingDir()
    {
        $this->assertTrue(empty($GLOBALS['module_mock_init_1']));
        $this->assertTrue(empty($GLOBALS['module_mock_init_2']));

        $application = new Application(new Request());
        $application->initModules(new ModulesLoader('foo'));

        $this->assertTrue(empty($GLOBALS['module_mock_init_1']));
        $this->assertTrue(empty($GLOBALS['module_mock_init_2']));
    }


    /**
     *
     */
    public function testAddModule()
    {

        $application = new Application(new Request());

        $module = $this->getMockBuilder('\Puppy\resources\ModuleMock')->getMock();
        $module->expects($this->once())
            ->method('init')
            ->with($application);

        $application->addModule($module);
    }

    /**
     *
     */
    public function testAddService()
    {
        $services = new Container();

        $application = new Application(new Request(), $services);
        $application->addService(
            'service1',
            function () {
                return 'value1';
            }
        );
        $this->assertSame('value1', $services['service1']);
    }
}
 