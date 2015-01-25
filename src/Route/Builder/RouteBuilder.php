<?php
namespace Puppy\Route\Builder;

use Puppy\Controller\AppController;
use Puppy\Route\Route;
use Puppy\Route\RoutePattern;

/**
 * Class RouteBuilder
 * @package Puppy\Route\Builder
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class RouteBuilder
{
    /**
     * @var string
     */
    private $uriPattern;

    /**
     * @var callable
     */
    private $controller;

    /**
     * @var string
     */
    private $method;

    /**
     * @var string
     */
    private $contentType;

    /**
     * Getter of $route
     *
     * @return Route
     */
    public function getRoute()
    {
        return new Route($this->getRoutePattern(), $this->controller);
    }

    /**
     * @param string $uriPattern
     * @return $this
     */
    public function addUriPattern($uriPattern)
    {
        $this->uriPattern = $uriPattern;
        return $this;
    }

    /**
     * @param callable $controller
     * @param AppController $appController
     * @return $this
     */
    public function addController(callable $controller, AppController $appController)
    {
        if ($controller instanceof \Closure) {
            $controller = \Closure::bind($controller, $appController);
        }
        $this->controller = $controller;
        return $this;
    }

    /**
     * @param string $method
     * @return $this
     */
    public function addMethod($method)
    {
        $this->method = $method;
        return $this;
    }

    /**
     * @param string $contentType
     * @return $this
     */
    public function addContentType($contentType)
    {
        $this->contentType = $contentType;
        return $this;
    }

    /**
     * @return RoutePattern
     */
    private function getRoutePattern()
    {
        $routePattern = new RoutePattern($this->uriPattern);
        $routePattern->setMethod($this->method);
        $routePattern->setContentType($this->contentType);
        return $routePattern;
    }
}
