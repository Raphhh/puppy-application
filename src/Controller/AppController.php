<?php
namespace Puppy\Controller;

use ArrayAccess;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

/**
 * Class AppController
 * util class for controller
 *
 * @package Puppy
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class AppController
{

    /**
     * @var ArrayAccess
     */
    private $services;

    /**
     * @param ArrayAccess $services
     */
    public function __construct(ArrayAccess $services = null)
    {
        if ($services) {
            $this->setServices($services);
        }
    }

    /**
     * renders a template.
     * uses template service.
     *
     * @param $template
     * @param array $vars
     * @return mixed
     */
    public function render($template, array $vars = array())
    {
        return $this->getService('template')->render($template, $vars);
    }

    /**
     * http redirection
     *
     * @param string $url
     * @param int $status
     * @param array $headers
     * @return RedirectResponse
     */
    public function redirect($url, $status = 302, array $headers = array())
    {
        return new RedirectResponse($url, $status, $headers);
    }

    /**
     * http error 404, page not found
     *
     * @return Response
     */
    public function error404()
    {
        return new Response('', 404);
    }

    /**
     * Gets the flash message manager.
     * uses session service.
     *
     * @return FlashBagInterface
     */
    public function flash()
    {
        return $this->getService('session')->getFlashBag();
    }

    /**
     * retrieves a param from
     *  - the matches of the uri
     *  - the request
     *  - the flash
     *
     * @param string $key
     * @param string $default
     * @return string
     */
    public function retrieve($key, $default = '')
    {
        $matches = $this->getService('router')->getCurrentRoute()->getMatches();
        if(isset($matches[$key])){
            return $matches[$key];
        }

        if(null !== $this->getService('request')->get($key)){
            return $this->getService('request')->get($key);
        }

        if($this->flash()->has($key)){
            return $this->flash()->get($key)[0];
        }

        return $default;
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
     * Setter of $services
     *
     * @param \ArrayAccess $services
     */
    private function setServices(\ArrayAccess $services)
    {
        $this->services = $services;
    }
}
 