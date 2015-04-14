<?php
namespace Puppy\Controller;

use Pimple\Container;
use Puppy\Service\ServiceContainer;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

/**
 * Class AppController
 * base controller with util methods
 *
 * @package Puppy
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class AppController
{
    use ServiceContainer;

    /**
     * @param Container $services
     */
    public function __construct(Container $services = null)
    {
        if ($services) {
            $this->setServices($services);
        }
    }

    /**
     * renders a template.
     * uses template service.
     *
     * @param string $templateFile
     * @param array $vars
     * @return mixed
     */
    public function render($templateFile, array $vars = [])
    {
        $this->getService('retriever')->setLocalVars($vars);
        return $this->getService('template')->render($templateFile, $vars);
    }

    /**
     * http redirection
     *
     * @param string $url
     * @param int $status
     * @param array $headers
     * @return RedirectResponse
     */
    public function redirect($url, $status = 302, array $headers = [])
    {
        return new RedirectResponse($url, $status, $headers);
    }

    /**
     *
     * internal call of another controller with a new request uri.
     * will not use http, but will call directly the FrontController to dispatch a new Request.
     *
     * the mirror is the uri that you call.
     * you can bind some args in this uri with the following syntax:
     * $mirror = '/uri/{variable}';
     * $args = ['variable' => 'value];
     * so, the final uri will be => '/uri/value'
     *
     * the other values of the request (method, content-type, ...) will be preserved.
     *
     * this method is used by Application::mirror(). So, prefer using this last method for a generic usage.
     *
     * @param string $mirror
     * @param array $args
     * @return Response
     */
    public function call($mirror, array $args = [])
    {
        $replacements = [];
        foreach($args as $key => $value){
            $replacements['{'.$key.'}'] = $value;
        }

        $newRequest = $this->getService('request')->duplicate();
        $newRequest->server->set('REQUEST_URI', strtr($mirror, $replacements));
        return $this->getService('frontController')->call($newRequest);
    }

    /**
     * http error 404, page not found
     *
     * @return Response
     * @deprecated use AppController::abort();
     */
    public function error404()
    {
        return new Response('', 404);
    }

    /**
     * aborts the script and sends an http code (404 by default)
     *
     * @param int $status
     * @return Response
     */
    public function abort($status = 404)
    {
        return new Response('', $status);
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
}
 