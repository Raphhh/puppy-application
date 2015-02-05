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
     * @var Route
     */
    private $currentRoute;

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
     * @param \ArrayAccess $services
     * @return Route
     */
    public function find(Request $request, \ArrayAccess $services)
    {
        $route = $this->getRouteFinder()->find($request, $this->getRoutes(), $services);
        $this->setCurrentRoute($route);
        return $route;
    }

    /**
     * returns the last route found, after having called the method Router::find().
     * If method Router::find has not been called or if no route has been found,
     * the method returns null.
     *
     * @return Route|null
     */
    public function getCurrentRoute()
    {
        return $this->currentRoute;
    }

    /**
     * Setter of $currentRoute.
     *
     * @param Route $currentRoute
     */
    private function setCurrentRoute($currentRoute)
    {
        $this->currentRoute = $currentRoute;
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
 