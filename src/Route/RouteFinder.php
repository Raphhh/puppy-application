<?php
namespace Puppy\Route;

use DomainException;
use Symfony\Component\HttpFoundation\Request;
use TRex\Reflection\CallableReflection;

/**
 * Class RouteFinderµ
 * Finds a route in matching its pattern with a uri.
 *
 * @package Puppy\Route
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class RouteFinder
{
    /**
     * Finds a route in matching its pattern with a uri.
     *
     * @param Request $request
     * @param Route[] $routes
     * @param \ArrayAccess $services
     * @return Route
     */
    public function find(Request $request, array $routes, \ArrayAccess $services)
    {
        foreach ($routes as $route) {

            if(!$this->matchMethod($request, $route)){
                continue;
            }

            if(!$this->matchContentType($request, $route)){
                continue;
            }

            if(!$this->matchFilters($route, $services)){
                continue;
            }

            $routeMatches = $this->matchPattern($request, $route);
            if ($routeMatches) {
                $route->setMatches($routeMatches);
                return $route;
            }
        }
        throw new RouteException(
            sprintf('No route found for uri "%s"', $request->getRequestUri()),
            $request,
            $routes
        );
    }

    /**
     * @param Request $request
     * @param Route $route
     * @return bool
     */
    private function matchMethod(Request $request, Route $route)
    {
        return !$route->getPattern()->getMethod() || $route->getPattern()->getMethod() === $request->getMethod();
    }

    /**
     * @param Request $request
     * @param Route $route
     * @return bool
     */
    private function matchContentType(Request $request, Route $route)
    {
        return !$route->getPattern()->getContentType()
            || in_array($route->getPattern()->getContentType(), $request->getAcceptableContentTypes());
    }

    /**
     * @param Request $request
     * @param Route $route
     * @return string[]
     */
    private function matchPattern(Request $request, Route $route)
    {
        $matches = array();
        @preg_match($route->getPattern()->getRegexUri(), $request->getRequestUri(), $matches); //todo catch the warning to an exception
        return $matches;
    }

    /**
     * @param Route $route
     * @param \ArrayAccess $services
     * @return bool
     */
    private function matchFilters(Route $route, \ArrayAccess $services)
    {
        foreach($route->getPattern()->getFilters() as $filter){
            $callbackReflection = new CallableReflection($filter);
            if(!$callbackReflection->invokeA((array)$services)){
                return false;
            }
        }
        return true;
    }
}
 