<?php
namespace Puppy;

use Pimple\Container;
use Puppy\Config\Config;
use Puppy\Module\ModulesLoader;
use Puppy\Module\ModulesLoaderProxy;
use Puppy\Route\Router;
use Stash\Pool;
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

    public function testRun()
    {
        $application = new Application(new Config(), new Request());
        $application->any('', function(){ return 'this is great!'; });

        ob_start();
        $application->run();
        $this->assertSame('this is great!', ob_get_clean());
    }

    public function testRunWithoutController()
    {
        $application = new Application(new Config(), new Request());
        $this->setExpectedException('Puppy\Route\RouteException', 'No route found for uri ""');
        $application->run();
    }

    public function testAny()
    {
        $controller = function(){};

        $application = new Application(new Config(), new Request());
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

        $application = new Application(new Config(), new Request());
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

        $application = new Application(new Config(), new Request());
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

        $application = new Application(new Config(), new Request());
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

        $application = new Application(new Config(), new Request());
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

        $application = new Application(new Config(), new Request());
        $application->initModules(new ModulesLoader('foo'));

        $this->assertTrue(empty($GLOBALS['module_mock_init_1']));
        $this->assertTrue(empty($GLOBALS['module_mock_init_2']));
    }

    public function testInitModulesWithProxy()
    {
        $this->assertTrue(empty($GLOBALS['module_mock_init_1']));
        $this->assertTrue(empty($GLOBALS['module_mock_init_2']));

        $application = new Application(new Config(), new Request());
        $application->initModules(new ModulesLoaderProxy(new ModulesLoader(), new Pool()));

        $this->assertFalse(empty($GLOBALS['module_mock_init_1']));
        $this->assertFalse(empty($GLOBALS['module_mock_init_2']));

        unset($GLOBALS['module_mock_init_1']);
        $this->assertTrue(empty($GLOBALS['module_mock_init_1']));

        unset($GLOBALS['module_mock_init_2']);
        $this->assertTrue(empty($GLOBALS['module_mock_init_2']));
    }

    /**
     *
     */
    public function testAddModule()
    {

        $application = new Application(new Config(), new Request());

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

        $application = new Application(new Config(), new Request(), $services);
        $application->addService(
            'service1',
            function () {
                return 'value1';
            }
        );
        $this->assertSame('value1', $services['service1']);
    }

    /**
     *
     */
    public function testGetServiceWithService()
    {
        $services = new Container();

        $application = new Application(new Config(), new Request(), $services);
        $application->addService(
            'service1',
            function () {
                return 'value1';
            }
        );
        $this->assertSame('value1', $application->getService('service1'));
    }

    /**
     *
     */
    public function testGetServiceWithoutService()
    {
        $services = new Container();
        $application = new Application(new Config(), new Request(), $services);

        $this->setExpectedException('\InvalidArgumentException', 'Service service1 not found');
        $application->getService('service1');
    }
}
 