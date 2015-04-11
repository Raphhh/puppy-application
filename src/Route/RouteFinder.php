<?php
namespace Puppy\Route;

use Pimple\Container;
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
     * @var Container
     */
    private $services;

    /**
     * @param Container $services
     */
    public function __construct(Container $services)
    {
        $this->setServices($services);
    }

    /**
     * Finds a route in matching its pattern with a uri.
     *
     * @param Request $request
     * @param Route[] $routes
     * @return Route
     */
    public function find(Request $request, array $routes)
    {
        foreach ($routes as $route) {

            if(!$this->matchMethod($request, $route)){
                continue;
            }

            if(!$this->matchContentType($request, $route)){
                continue;
            }

            if(!$this->matchFilters($route)){
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
        preg_match($route->getPattern()->getRegexUri(), $request->getRequestUri(), $matches); //todo catch the warning to an exception
        return $matches;
    }

    /**
     * @param Route $route
     * @return bool
     */
    private function matchFilters(Route $route)
    {
        foreach($route->getPattern()->getFilters() as $filter){
            if(!$this->callFilter($filter)){
                return false;
            }
        }
        return true;
    }

    /**
     * @param callable $filter
     * @return mixed
     */
    private function callFilter(callable $filter)
    {
        $callbackReflection = new CallableReflection($filter);
        return $callbackReflection->invokeA($this->castServices($callbackReflection->getReflector()->getParameters()));
    }

    /**
     * @param \ReflectionParameter[] $callableParams
     * @return array
     */
    private function castServices(array $callableParams)
    {
        $result = [];
        foreach($callableParams as $param){
            if(isset($this->services[$param->name])){
                $result[$param->name] = $this->services[$param->name];
            }
        }
        return $result;
    }

    /**
     * Setter of $services
     *
     * @param Container $services
     */
    private function setServices(Container $services)
    {
        $this->services = $services;
    }
}
 