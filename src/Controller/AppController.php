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
        $this->getService('retriever')->setLocalVars($vars);
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
        $retriever = $this->getService('retriever');
        if($retriever->has($key)){
            return $retriever->get($key);
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
 