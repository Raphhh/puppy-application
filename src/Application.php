<?php
namespace Puppy;

use ArrayAccess;
use Pimple\Container;
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
     * @param Request $request
     * @param \ArrayAccess $services
     */
    public function __construct(Request $request, ArrayAccess $services = null)
    {
        $services = $services ? : new Container();
        $this->setServices($services);
        $this->initServices($request, $services);
    }

    /**
     * @return Http\IResponse
     */
    public function run()
    {
        return $this->getFrontController()->call();
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
        return $this->getServices()['frontController'];
    }

    /**
     * @param Request $request
     * @param ArrayAccess $services
     */
    private function initServices(Request $request, ArrayAccess $services){

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
