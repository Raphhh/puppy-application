<?php
namespace Puppy;

use ArrayAccess;
use Pimple\Container;
use Puppy\Controller\AppController;
use Puppy\Controller\FrontController;
use Puppy\Helper\Retriever;
use Puppy\Module\IModule;
use Puppy\Module\IModulesLoader;
use Puppy\Process\Processes;
use Puppy\Route\Group;
use Puppy\Route\IRoutePatternSetter;
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
     * @var Processes
     */
    private $preProcesses;

    /**
     * @var Processes
     */
    private $postProcesses;

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

        $this->setPreProcesses(new Processes());
        $this->setPostProcesses(new Processes());
    }

    /**
     * Sends the http response
     */
    public function run()
    {
        $request = $this->getService('requestStack')->getMasterRequest();
        $this->getPreProcesses()->execute($request);
        $response = $this->getFrontController()->call($request);
        $this->getPostProcesses()->execute($response);
        $response->send();
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
     * @return IRoutePatternSetter
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
     * @return IRoutePatternSetter
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
     * @return IRoutePatternSetter
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
     * @return IRoutePatternSetter
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
     * @return IRoutePatternSetter
     */
    public function filter(callable $filter, callable $controller)
    {
        $pattern = $this->getFrontController()->addController(':all', $controller)->getPattern();
        $pattern->addFilter($filter);
        return new IRoutePatternSetterAdapter($pattern);
    }

    /**
     * groups several routes.
     *
     * @param IRoutePatternSetter[] $iRoutePatterns
     * @return IRoutePatternSetter
     */
    public function group(array $iRoutePatterns)
    {
        return new Group($iRoutePatterns);
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
     * adds a process before the call stack.
     * $process will receive the master request as first arg.
     *
     * @param callable $process
     */
    public function before(callable $process)
    {
        if ($process instanceof \Closure) {
            $process = \Closure::bind($process, $this);
        }
        $this->preProcesses[] = $process;
    }

    /**
     *
     * adds a process after the call stack.
     * $process will receive the master response as first arg.
     *
     * @param callable $process
     */
    public function after(callable $process)
    {
        if ($process instanceof \Closure) {
            $process = \Closure::bind($process, $this);
        }
        $this->postProcesses[] = $process;
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

    /**
     * Getter of $preProcesses
     *
     * @return Processes
     */
    private function getPreProcesses()
    {
        return $this->preProcesses;
    }

    /**
     * Setter of $preProcesses
     *
     * @param Processes $preProcesses
     */
    private function setPreProcesses(Processes $preProcesses)
    {
        $this->preProcesses = $preProcesses;
    }

    /**
     * Getter of $postProcesses
     *
     * @return Processes
     */
    private function getPostProcesses()
    {
        return $this->postProcesses;
    }

    /**
     * Setter of $postProcesses
     *
     * @param Processes $postProcesses
     */
    private function setPostProcesses(Processes $postProcesses)
    {
        $this->postProcesses = $postProcesses;
    }
}
