<?php
namespace Puppy\Service;

use Pimple\Container;

/**
 * Class ServiceContainer
 * @package Puppy\Service
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
trait ServiceContainer
{
    /**
     * @var Container
     */
    private $services;

    /**
     * Adds a service to the services container.
     *
     * @param string $name
     * @param mixed $service
     */
    public function addService($name, $service)
    {
        $this->getServices()->offsetSet($name, $service);
    }

    /**
     * @return Container
     */
    public function getServices()
    {
        return $this->services;
    }

    /**
     * Getter of a service
     *
     * @param string $service
     * @throws \InvalidArgumentException
     * @return mixed
     */
    public function getService($service)
    {
        if (!isset($this->services[$service])) {
            throw new \InvalidArgumentException(sprintf('Service %s not found', $service));
        }
        return $this->services[$service];
    }

    /**
     * @param Container $services
     */
    public function setServices(Container $services)
    {
        $this->services = $services;
    }
}
