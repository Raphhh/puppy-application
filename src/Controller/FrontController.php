<?php
namespace Puppy\Controller;

use ArrayAccess;
use Puppy\Route\Builder\RouteBuilder;
use Puppy\Route\Router;
use Symfony\Component\HttpFoundation\Request;
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
     * @var ArrayAccess
     */
    private $services;

    /**
     * Constructor.
     *
     * @param ArrayAccess $services
     * @throws \InvalidArgumentException
     */
    public function __construct(ArrayAccess $services)
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
            ->addController($controller, $this->getServices()['appController'])
            ->addMethod($method)
            ->addContentType($contentType);

        $route = $routeBuilder->getRoute();
        $this->getRouter()->addRoute($route);
        return $route;
    }

    /**
     * Calls the controller matched with the request uri.
     *
     * @return Response
     */
    public function call()
    {
        $response = $this->getRouter()
                ->find($this->getRequest(), $this->getServices())
                ->call($this->getServices());

        if($response instanceof Response){
            return $response;
        }
        return new Response($response);
    }

    /**
     * @return Request
     */
    private function getRequest()
    {
        return $this->getServices()['request'];
    }

    /**
     * @return Router
     */
    private function getRouter()
    {
        return $this->getServices()['router'];
    }

    /**
     * @param ArrayAccess $services
     */
    private function setServices(ArrayAccess $services)
    {
        $this->services = $services;
    }

    /**
     * @return ArrayAccess
     */
    private function getServices()
    {
        return $this->services;
    }
}
