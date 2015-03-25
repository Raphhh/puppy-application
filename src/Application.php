<?php
namespace Puppy;

use ArrayAccess;
use Pimple\Container;
use Puppy\Controller\AppController;
use Puppy\Controller\FrontController;
use Puppy\Helper\Retriever;
use Puppy\Module\IModule;
use Puppy\Module\IModulesLoader;
use Puppy\Route\IRoutePatternSetterAdapter;
use Puppy\Route\RouteFinder;
use Puppy\Route\Router;
use Puppy\Service\ServiceContainer;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Application
 * Manages the controllers, the services and the modules.
 *
 * @package Puppy
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class Application
{
    use ServiceContainer;

    /**
     * Constructor.
     *
     * @param \ArrayAccess $config
     * @param Request $request
     * @param \ArrayAccess $services
     */
    public function __construct(\ArrayAccess $config, Request $request, ArrayAccess $services = null)
    {
        $services = $services ? : new Container();
        $this->setServices($services);
        $this->initServices($config, $request);
    }

    /**
     * Sends the http response
     */
    public function run()
    {
        $this->getFrontController()->call()->send();
    }

    /**
     * Init the modules of the current project.
     *
     * @param IModulesLoader $modulesLoader
     */
    public function initModules(IModulesLoader $modulesLoader)
    {
        foreach ($modulesLoader->getModules() as $module) {
            $this->addModule($module);
        }
    }

    /**
     * Adds an independent module.
     * the module can add services or controllers in its init method.
     *
     * @param IModule $module
     */
    public function addModule(IModule $module)
    {
        $module->init($this);
    }

    /**
     * Adds a controller called by http GET method.
     *
     * @param string $uriPattern
     * @param callable $controller
     * @return IRoutePatternSetterAdapter
     */
    public function get($uriPattern, callable $controller)
    {
        return new IRoutePatternSetterAdapter(
            $this->getFrontController()->addController($uriPattern, $controller, 'get')->getPattern()
        );
    }

    /**
     * Adds a controller called by http POST method.
     *
     * @param string $uriPattern
     * @param callable $controller
     * @return IRoutePatternSetterAdapter
     */
    public function post($uriPattern, callable $controller)
    {
        return new IRoutePatternSetterAdapter(
            $this->getFrontController()->addController($uriPattern, $controller, 'post')->getPattern()
        );
    }

    /**
     * Adds a controller with a json format.
     *
     * @param string $uriPattern
     * @param callable $controller
     * @return IRoutePatternSetterAdapter
     */
    public function json($uriPattern, callable $controller)
    {
        return new IRoutePatternSetterAdapter(
            $this->getFrontController()->addController($uriPattern, $controller, '', 'application/json')->getPattern()
        );
    }

    /**
     * Adds a controller called by any http method or format.
     *
     * @param string $uriPattern
     * @param callable $controller
     * @return IRoutePatternSetterAdapter
     */
    public function any($uriPattern, callable $controller)
    {
        return new IRoutePatternSetterAdapter(
            $this->getFrontController()->addController($uriPattern, $controller)->getPattern()
        );
    }

    /**
     * Adds a controller with a special filter.
     * This filter receive the current Request as first param.
     *
     * @param callable $filter
     * @param callable $controller
     * @return IRoutePatternSetterAdapter
     */
    public function filter(callable $filter, callable $controller)
    {
        $pattern = $this->getFrontController()->addController(':all', $controller)->getPattern();
        $pattern->addFilter($filter);
        return new IRoutePatternSetterAdapter($pattern);
    }

    /**
     * Getter of $frontController
     *
     * @return FrontController
     */
    private function getFrontController()
    {
        return $this->getService('frontController');
    }

    /**
     * @param \ArrayAccess $config
     * @param Request $request
     */
    private function initServices(\ArrayAccess $config, Request $request){

        $this->addService(
            'config',
            function () use ($config) {
                return $config;
            }
        );

        $this->addService(
            'frontController',
            function (ArrayAccess $services) {
                return new FrontController($services);
            }
        );

        $this->addService(
            'request',
            function () use ($request) {
                return $request;
            }
        );

        $this->addService(
            'router',
            function () {
                return new Router(new RouteFinder());
            }
        );

        $this->addService(
            'appController',
            function(ArrayAccess $services){
                return new AppController($services);
            }
        );

        $this->addService(
            'retriever',
            function(ArrayAccess $services){
                return new Retriever($services['router'], $services['request'], $services['session']->getFlashBag());
            }
        );
    }
}
