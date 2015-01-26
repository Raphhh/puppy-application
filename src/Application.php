<?php
namespace Puppy;

use ArrayAccess;
use Pimple\Container;
use Puppy\Config\IConfig;
use Puppy\Controller\FrontController;
use Puppy\Module\IModule;
use Puppy\Module\ModulesLoader;
use Puppy\Route\RouteFinder;
use Puppy\Route\Router;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Application
 * Manages the FrontController and the modules.
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
     * @param IConfig $config
     * @param Request $request
     * @param \ArrayAccess $services
     */
    public function __construct(IConfig $config, Request $request, ArrayAccess $services = null)
    {
        $services = $services ? : new Container();
        $this->setServices($services);
        $this->initServices($config, $request, $services);
    }

    /**
     *
     */
    public function run()
    {
        $this->getFrontController()->call()->send();
    }

    /**
     * @param ModulesLoader $modulesLoader
     */
    public function initModules(ModulesLoader $modulesLoader)
    {
        foreach ($modulesLoader->getModules() as $module) {
            $this->addModule($module);
        }
    }

    /**
     * add an independent module.
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
     * @param string $uriPattern
     * @param callable $controller
     */
    public function get($uriPattern, callable $controller)
    {
        $this->getFrontController()->addController($uriPattern, $controller, 'get');
    }

    /**
     * @param string $uriPattern
     * @param callable $controller
     */
    public function post($uriPattern, callable $controller)
    {
        $this->getFrontController()->addController($uriPattern, $controller, 'post');
    }

    /**
     * @param string $uriPattern
     * @param callable $controller
     */
    public function json($uriPattern, callable $controller)
    {
        $this->getFrontController()->addController($uriPattern, $controller, '', 'application/json');
    }

    /**
     * @param string $uriPattern
     * @param callable $controller
     */
    public function any($uriPattern, callable $controller)
    {
        $this->getFrontController()->addController($uriPattern, $controller);
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
     * @param Config\IConfig $config
     * @param Request $request
     * @param ArrayAccess $services
     */
    private function initServices(IConfig $config, Request $request, ArrayAccess $services){

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
