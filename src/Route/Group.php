<?php
namespace Puppy\Route;

/**
 * Class Group
 * @package Route
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class Group extends \ArrayObject implements IRoutePatternSetter
{
    /**
     * @param string $method
     * @return IRoutePatternSetter
     */
    public function method($method)
    {
        /**
         * @var IRoutePatternSetter[] $this
         */
        foreach($this as $routePatternSetter){
            $routePatternSetter->method($method);
        }
        return $this;
    }

    /**
     * @param string $acceptableContent
     * @return IRoutePatternSetter
     */
    public function content($acceptableContent)
    {
        /**
         * @var IRoutePatternSetter[] $this
         */
        foreach($this as $routePatternSetter){
            $routePatternSetter->content($acceptableContent);
        }
        return $this;
    }

    /**
     * @param callable $middleware
     * @return IRoutePatternSetter
     */
    public function filter(callable $middleware)
    {
        /**
         * @var IRoutePatternSetter[] $this
         */
        foreach($this as $routePatternSetter){
            $routePatternSetter->filter($middleware);
        }
        return $this;
    }

    /**
     * @param string $alias
     * @param string $pattern
     * @param string $delimiter
     * @return IRoutePatternSetter
     */
    public function bind($alias, $pattern = '[a-zA-Z0-9\-_]+', $delimiter = ':')
    {
        /**
         * @var IRoutePatternSetter[] $this
         */
        foreach($this as $routePatternSetter){
            $routePatternSetter->bind($alias, $pattern, $delimiter);
        }
        return $this;
    }

    /**
     * @param string $namespace
     * @return IRoutePatternSetter
     */
    public function restrict($namespace)
    {
        /**
         * @var IRoutePatternSetter[] $this
         */
        foreach($this as $routePatternSetter){
            $routePatternSetter->restrict($namespace);
        }
        return $this;
    }
}
