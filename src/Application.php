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
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;

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
     * @param Request $masterRequest
     * @param Container $services
     */
    public function __construct(\ArrayAccess $config, Request $masterRequest, Container $services = null)
    {
        $this->setServices($services ? : new Container());
        $this->initServices($config, $masterRequest);
    }

    /**
     * Sends the http response
     */
    public function run()
    {
        $this->getFrontController()->call($this->getService('requestStack')->getMasterRequest())->send();
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
     * Adds a mirror of a route.
     *
     * @param string $uriPattern
     * @param string $mirror
     * @return IRoutePatternSetterAdapter
     */
    public function mirror($uriPattern, $mirror)
    {
        return $this->any(
            $uriPattern,
            function (AppController $appController, array $args) use ($mirror) {
                return $appController->call($mirror, $args);
            }
        );
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
     * @param Request $masterRequest
     */
    private function initServices(\ArrayAccess $config, Request $masterRequest){

        $this->addService(
            'config',
            function () use ($config) {
                return $config;
            }
        );

        $this->addService(
            'frontController',
            function (Container $services) {
                return new FrontController($services);
            }
        );

        $this->addService(
            'requestStack',
            function () use ($masterRequest) {
                $requestStack = new RequestStack();
                $requestStack->push($masterRequest);
                return $requestStack;
            }
        );

        $this->addService(
            'request', //current request
            $this->getServices()->factory(function (ArrayAccess $services){
                return $services['requestStack']->getCurrentRequest();
            })
        );

        $this->addService(
            'router',
            function (Container $services) {
                return new Router(new RouteFinder($services));
            }
        );

        $this->addService(
            'appController',
            function(Container $services){
                return new AppController($services);
            }
        );

        $this->addService(
            'retriever',
            function(Container $services){
                return new Retriever(
                    $services['router'],
                    $services['requestStack'],
                    isset($services['session']) ? $services['session']->getFlashBag() : new FlashBag()
                );
            }
        );
    }
}
