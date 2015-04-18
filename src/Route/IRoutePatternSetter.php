<?php
namespace Puppy\Route;

/**
 * Interface IRoutePatternSetter
 * @package Route
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
interface IRoutePatternSetter
{
    /**
     * @param string $method
     * @return IRoutePatternSetter
     */
    public function method($method);

    /**
     * @param string $acceptableContent
     * @return IRoutePatternSetter
     */
    public function content($acceptableContent);

    /**
     * @param callable $middleware
     * @return IRoutePatternSetter
     */
    public function filter(callable $middleware);

    /**
     * @param string $alias
     * @param string $pattern
     * @param string $delimiter
     * @return IRoutePatternSetter
     */
    public function bind($alias, $pattern = '[a-zA-Z0-9\-_]+', $delimiter = ':');

    /**
     * @param string $namespace
     * @return IRoutePatternSetter
     */
    public function restrict($namespace);
}
