<?php
namespace Puppy\Controller;

use Pimple\Container;
use Puppy\Route\Builder\RouteBuilder;
use Puppy\Route\Router;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class FrontController
 * Controls the app controllers.
 *
 * @package Puppy
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class FrontController
{

    /**
     * @var Container
     */
    private $services;

    /**
     * Constructor.
     *
     * @param Container $services
     * @throws \InvalidArgumentException
     */
    public function __construct(Container $services)
    {
        $this->setServices($services);
    }

    /**
     * Adds a controller to the list of observed controllers.
     *
     * @param string $uriPattern
     * @param callable $controller
     * @param string $method
     * @param string $contentType
     * @return \Puppy\Route\Route
     */
    public function addController($uriPattern, callable $controller, $method='', $contentType='')
    {
        $routeBuilder = new RouteBuilder();
        $routeBuilder
            ->addUriPattern($uriPattern)
            ->addController($controller, $this->getAppController())
            ->addMethod($method)
            ->addContentType($contentType);

        $route = $routeBuilder->getRoute();
        $this->getRouter()->addRoute($route);
        return $route;
    }

    /**
     * Calls the controller matched with the request uri.
     *
     * @param Request $request
     * @return Response
     */
    public function call(Request $request)
    {
        if($request !== $this->getRequestStack()->getCurrentRequest()){
            $this->getRequestStack()->push($request);
        }

        if(!$request->getRequestFormat(null) && $request->getAcceptableContentTypes()){
            $request->setRequestFormat($request->getFormat($request->getAcceptableContentTypes()[0]));
        }

        $response = $this->getRouter()
                ->find($request, $this->getServices())
                ->call($this->getServices());

        if($response instanceof Response){
            return $response;
        }
        return new Response($response);
    }

    /**
     * @return Router
     */
    private function getRouter()
    {
        return $this->getServices()['router'];
    }

    /**
     * @return AppController
     */
    private function getAppController()
    {
        return $this->getServices()['appController'];
    }

    /**
     * @return RequestStack
     */
    private function getRequestStack()
    {
        return $this->getServices()['requestStack'];
    }

    /**
     * @param Container $services
     */
    private function setServices(Container $services)
    {
        $this->services = $services;
    }

    /**
     * @return Container
     */
    private function getServices()
    {
        return $this->services;
    }
}
