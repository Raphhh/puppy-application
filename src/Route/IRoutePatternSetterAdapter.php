<?php
namespace Puppy\Route;

/**
 * Class IRoutePatternSetterAdapter
 * adapts a RoutePattern to a IRoutePatternSetter
 *
 * @package Route
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class IRoutePatternSetterAdapter implements IRoutePatternSetter
{

    /**
     * @var RoutePattern
     */
    private $routePattern;

    /**
     * @param RoutePattern $routePattern
     */
    public function __construct(RoutePattern $routePattern)
    {
        $this->setRoutePattern($routePattern);
    }

    /**
     * @param string $method
     * @return $this
     */
    public function method($method)
    {
        $this->getRoutePattern()->setMethod($method);
        return $this;
    }

    /**
     * @param string $acceptableContentType
     * @return $this
     */
    public function content($acceptableContentType)
    {
        $this->getRoutePattern()->setContentType($acceptableContentType);
        return $this;
    }

    /**
     * @param callable $middleware
     * @return $this
     */
    public function filter(callable $middleware)
    {
        $this->getRoutePattern()->addFilter($middleware);
        return $this;
    }

    /**
     * @param string $alias
     * @param string $pattern
     * @param string $delimiter
     * @return $this
     */
    public function bind($alias, $pattern, $delimiter = ':')
    {
        $this->getRoutePattern()->addBinding($alias, $pattern, $delimiter);
        return $this;
    }

    /**
     * Getter of $routePattern
     *
     * @return RoutePattern
     */
    private function getRoutePattern()
    {
        return $this->routePattern;
    }

    /**
     * Setter of $routePattern
     *
     * @param RoutePattern $routePattern
     */
    private function setRoutePattern(RoutePattern $routePattern)
    {
        $this->routePattern = $routePattern;
    }
}
