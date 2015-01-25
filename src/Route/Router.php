<?php
namespace Puppy\Route;

use Symfony\Component\HttpFoundation\Request;

class Router
{
    /**
     * @var Route[]
     */
    private $routes = array();

    /**
     * @var RouteFinder
     */
    private $routeFinder;

    /**
     * Constructor
     *
     * @param RouteFinder $routeFinder
     */
    public function __construct(RouteFinder $routeFinder)
    {
        $this->setRouteFinder($routeFinder);
    }

    /**
     * Gets the routes.
     *
     * @return Route[]
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * Adds a route.
     *
     * @param Route $route
     */
    public function addRoute(Route $route)
    {
        $this->routes[] = $route;
    }

    /**
     * Finds a route from a uri.
     *
     * @param Request $request
     * @return Route
     */
    public function find(Request $request)
    {
        return $this->getRouteFinder()->find($request, $this->getRoutes());
    }

    /**
     * Setter of $routeFinder
     *
     * @param RouteFinder $routeFinder
     */
    private function setRouteFinder(RouteFinder $routeFinder)
    {
        $this->routeFinder = $routeFinder;
    }

    /**
     * Getter of $routeFinder
     *
     * @return RouteFinder
     */
    private function getRouteFinder()
    {
        return $this->routeFinder;
    }
}
 