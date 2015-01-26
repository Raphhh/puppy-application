<?php
namespace Puppy\Route;

use Symfony\Component\HttpFoundation\Request;

/**
 * Class RouteException
 * @package Puppy\Route
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class RouteException extends \DomainException
{

    /**
     * @var Request
     */
    private $request;

    /**
     * @var Route[]
     */
    private $routes;

    /**
     * @param string $message
     * @param Request $request
     * @param Route[] $routes
     */
    public function __construct($message, Request $request, array $routes)
    {
        $this->setRequest($request);
        $this->setRoutes($routes);
        parent::__construct($message . $this->buildAdditionalMessage());
    }

    /**
     * Setter of $request
     *
     * @param Request $request
     */
    private function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Getter of $request
     *
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Setter of $routes
     *
     * @param Route[] $routes
     */
    private function setRoutes(array $routes)
    {
        $this->routes = $routes;
    }

    /**
     * Getter of $routes
     *
     * @return Route[]
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * @return string
     */
    private function buildAdditionalMessage()
    {
        if($this->getRoutes()){
            return "\nPossible routes:\n" . implode("\n", $this->getRoutes());
        }
        return '';
    }
}
