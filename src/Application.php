<?php
namespace Puppy;

use ArrayAccess;
use Pimple\Container;
use Puppy\Controller\FrontController;
use Puppy\Module\IModule;
use Puppy\Module\IModulesLoader;
use Puppy\Route\IRoutePatternSetterAdapter;
use Puppy\Route\RouteFinder;
use Puppy\Route\Router;
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

    /**
     * @var ArrayAccess
     */
    private $services;

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
        $this->initServices($config, $request, $services);
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
     * Adds a service to the services container.
     *
     * @param string $name
     * @param mixed $service
     */
    public function addService($name, $service)
    {
        $this->getServices()->offsetSet($name, $service);
    }

    /**
     * @return ArrayAccess
     */
    public function getServices()
    {
        return $this->services;
    }

    /**
     * Getter of a service
     *
     * @param string $service
     * @throws \InvalidArgumentException
     * @return mixed
     */
    public function getService($service)
    {
        if (!isset($this->services[$service])) {
            throw new \InvalidArgumentException(sprintf('Service %s not found', $service));
        }
        return $this->services[$service];
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
        //todo
    }

    /**
     * @param ArrayAccess $services
     */
    private function setServices(ArrayAccess $services)
    {
        $this->services = $services;
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
     * @param ArrayAccess $services
     */
    private function initServices(\ArrayAccess $config, Request $request, ArrayAccess $services){

        $this->addService(
            'config',
            function () use ($config) {
                return $config;
            }
        );

        $this->addService(
            'frontController',
            function () use ($services) {
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
    }
}
