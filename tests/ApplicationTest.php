<?php
namespace Puppy;

use Pimple\Container;
use Puppy\Controller\FrontController;
use Puppy\Helper\Retriever;
use Puppy\Module\ModulesLoader;
use Puppy\Module\ModulesLoaderProxy;
use Puppy\Route\Router;
use Stash\Pool;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

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
        $application = new Application(new \ArrayObject(), new Request());
        $application->any('', function(){ return 'this is great!'; });

        ob_start();
        $application->run();
        $this->assertSame('this is great!', ob_get_clean());
    }

    public function testRunWithoutController()
    {
        $application = new Application(new \ArrayObject(), new Request());
        $this->setExpectedException('Puppy\Route\RouteException', 'No route found for uri ""');
        $application->run();
    }

    public function testAny()
    {
        $controller = function(){};

        $application = new Application(new \ArrayObject(), new Request());
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

    public function testAnyWithAdditionalFilter()
    {
        $controller = function(){};

        $application = new Application(new \ArrayObject(), new Request());
        $application->any('pattern', $controller)->method('method')->content('content');

        /**
         * @var Router $router
         */
        $router = $application->getServices()['router'];
        $route = $router->getRoutes()[0];
        $this->assertSame('pattern', $route->getPattern()->getUri());
        $this->assertSame('METHOD', $route->getPattern()->getMethod());
        $this->assertSame('content', $route->getPattern()->getContentType());
    }

    public function testGet()
    {
        $controller = function(){};

        $application = new Application(new \ArrayObject(), new Request());
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

    public function testGetWithAdditionalFilter()
    {
        $controller = function(){};

        $application = new Application(new \ArrayObject(), new Request());
        $application->get('pattern', $controller)->method('method')->content('content');

        /**
         * @var Router $router
         */
        $router = $application->getServices()['router'];
        $route = $router->getRoutes()[0];
        $this->assertSame('pattern', $route->getPattern()->getUri());
        $this->assertSame('METHOD', $route->getPattern()->getMethod());
        $this->assertSame('content', $route->getPattern()->getContentType());
    }

    public function testPost()
    {
        $controller = function(){};

        $application = new Application(new \ArrayObject(), new Request());
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

    public function testPostWithAdditionalFilter()
    {
        $controller = function(){};

        $application = new Application(new \ArrayObject(), new Request());
        $application->post('pattern', $controller)->method('method')->content('content');

        /**
         * @var Router $router
         */
        $router = $application->getServices()['router'];
        $route = $router->getRoutes()[0];
        $this->assertSame('pattern', $route->getPattern()->getUri());
        $this->assertSame('METHOD', $route->getPattern()->getMethod());
        $this->assertSame('content', $route->getPattern()->getContentType());
    }

    public function testJson()
    {
        $controller = function(){};

        $application = new Application(new \ArrayObject(), new Request());
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

    public function testJsonWithAdditionalFilter()
    {
        $controller = function(){};

        $application = new Application(new \ArrayObject(), new Request());
        $application->json('pattern', $controller)->method('method')->content('content');

        /**
         * @var Router $router
         */
        $router = $application->getServices()['router'];
        $route = $router->getRoutes()[0];
        $this->assertSame('pattern', $route->getPattern()->getUri());
        $this->assertSame('METHOD', $route->getPattern()->getMethod());
        $this->assertSame('content', $route->getPattern()->getContentType());
    }

    public function testFilter()
    {
        $filter = function(){};
        $controller = function(){};

        $application = new Application(new \ArrayObject(), new Request());
        $application->filter($filter, $controller)->method('method')->content('content');

        /**
         * @var Router $router
         */
        $router = $application->getServices()['router'];
        $route = $router->getRoutes()[0];
        $this->assertSame(':all', $route->getPattern()->getUri());
        $this->assertSame('METHOD', $route->getPattern()->getMethod());
        $this->assertSame('content', $route->getPattern()->getContentType());
        $this->assertSame([$filter], $route->getPattern()->getFilters());
    }

    public function testFilterWithAdditionalFilter()
    {
        $filter = function(){};
        $controller = function(){};

        $application = new Application(new \ArrayObject(), new Request());
        $application->filter($filter, $controller);

        /**
         * @var Router $router
         */
        $router = $application->getServices()['router'];
        $route = $router->getRoutes()[0];
        $this->assertSame(':all', $route->getPattern()->getUri());
        $this->assertSame('', $route->getPattern()->getMethod());
        $this->assertSame('', $route->getPattern()->getContentType());
        $this->assertSame([$filter], $route->getPattern()->getFilters());
    }

    public function testInitModules()
    {
        $this->assertTrue(empty($GLOBALS['module_mock_init_1']));
        $this->assertTrue(empty($GLOBALS['module_mock_init_2']));

        $application = new Application(new \ArrayObject(), new Request());
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

        $application = new Application(new \ArrayObject(), new Request());
        $application->initModules(new ModulesLoader(['foo']));

        $this->assertTrue(empty($GLOBALS['module_mock_init_1']));
        $this->assertTrue(empty($GLOBALS['module_mock_init_2']));
    }

    public function testInitModulesWithProxy()
    {
        $this->assertTrue(empty($GLOBALS['module_mock_init_1']));
        $this->assertTrue(empty($GLOBALS['module_mock_init_2']));

        $application = new Application(new \ArrayObject(), new Request());
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

        $application = new Application(new \ArrayObject(), new Request());

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

        $application = new Application(new \ArrayObject(), new Request(), $services);
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

        $application = new Application(new \ArrayObject(), new Request(), $services);
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
        $application = new Application(new \ArrayObject(), new Request(), $services);

        $this->setExpectedException('\InvalidArgumentException', 'Service service1 not found');
        $application->getService('service1');
    }

    public function testAddAppControllerToServices()
    {

        $services = new Container();
        new Application(new \ArrayObject(), new Request(), $services);

        $this->assertArrayHasKey('appController', $services);
        $this->assertInstanceOf('Puppy\Controller\AppController', $services['appController']);
    }

    public function testRequestService()
    {
        $this->expectOutputString('mastermaster');

        //define the master request

        $masterRequest = new Request();
        $masterRequest->server->set('REQUEST_URI', 'master');
        $masterRequest->request->set('request', 'master');

        $application = new Application(new \ArrayObject(), $masterRequest);
        $application->any(':all', function(Request $request, Retriever $retriever){
            $retriever->setLocalVars(['local' => $request->getRequestUri()]);
            return $request->getRequestUri();
        });

        $this->assertSame($masterRequest, $application->getService('request'));
        $application->run(); //expect first "master" output
        $this->assertSame('master', $application->getService('retriever')->get('request'));
        $this->assertSame('master', $application->getService('retriever')->get('local'));

        //define another current request

        $currentRequest = new Request();
        $currentRequest->server->set('REQUEST_URI', 'current');
        $currentRequest->request->set('request', 'current');

        /**
         * @var FrontController $frontController
         */
        $frontController = $application->getService('frontController');
        $response = $frontController->call($currentRequest);

        $this->assertSame($currentRequest, $application->getService('request'));
        $this->assertSame('current', $response->getContent());
        $this->assertSame('current', $application->getService('retriever')->get('request'));
        $this->assertSame('current', $application->getService('retriever')->get('local'));
        $application->run(); //expect second "master" output
    }

    public function testCallWithNewRequest()
    {
        $masterRequest = new Request();
        $masterRequest->server->set('REQUEST_URI', '1');

        $application = new Application(new \ArrayObject(), $masterRequest);

        $application->any('1', function(FrontController $frontController){
            $currentRequest = new Request();
            $currentRequest->server->set('REQUEST_URI', '2');
            return $frontController->call($currentRequest);
        });

        $application->any('2', function(Request $request, RequestStack $requestStack){
            return $requestStack->getMasterRequest()->getRequestUri()
                . '-' . $requestStack->getCurrentRequest()->getRequestUri()
                . '-' . $request->getRequestUri();
        });

        $this->expectOutputString('1-2-2');
        $application->run();
    }

    public function testMirror()
    {
        $masterRequest = new Request();
        $masterRequest->server->set('REQUEST_URI', 'mail');

        $application = new Application(new \ArrayObject(), $masterRequest);
        $application->mirror('mail', 'contact');
        $application->get('contact', function(Request $request){
            return $request->getRequestUri();
        });

        $this->expectOutputString('contact');
        $application->run();
    }

    public function testMirrorWithParams()
    {
        $masterRequest = new Request();
        $masterRequest->server->set('REQUEST_URI', 'mail/123/456');

        $application = new Application(new \ArrayObject(), $masterRequest);
        $application->mirror('mail/:id/:index', 'contact/{id}/{id}');
        $application->get('contact/:id/:index', function(Request $request){
            return $request->getRequestUri();
        });

        $this->expectOutputString('contact/123/123');
        $application->run();
    }

    public function testGroup()
    {
        $masterRequest = new Request();
        $masterRequest->server->set('REQUEST_URI', 'mail/123/456');

        $application = new Application(new \ArrayObject(), $masterRequest);

        $application->group(
            [
                $application->get(
                    'uri1',
                    function (Request $request) {
                        return $request->getRequestUri();
                    }
                ),
                $application->get(
                    'uri2',
                    function (Request $request) {
                        return $request->getRequestUri();
                    }
                ),
            ]
        )
            ->restrict('admin');

        $routes = $application->getService('router')->getRoutes();
        $this->assertCount(2, $routes);
        $this->assertSame('admin/uri1', $routes[0]->getPattern()->getUri());
        $this->assertSame('admin/uri2', $routes[1]->getPattern()->getUri());
    }
}
 