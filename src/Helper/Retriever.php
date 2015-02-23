<?php
namespace Puppy\Helper;

use Puppy\Route\Router;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

/**
 * Class Retriever
 * @package Helper
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class Retriever
{

    /**
     * @var Router
     */
    private $router;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var FlashBagInterface
     */
    private $flash;

    /**
     * @var array
     */
    private $localVars = [];

    /**
     * @param Router $router
     * @param Request $request
     * @param FlashBagInterface $flashBag
     */
    public function __construct(Router $router, Request $request, FlashBagInterface $flashBag)
    {
        $this->setRouter($router);
        $this->setRequest($request);
        $this->setFlash($flashBag);
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has($key)
    {
        $vars = $this->getLocalVars();
        if(isset($vars[$key])){
            return true;
        }

        $matches = $this->getMatches();
        if(isset($matches[$key])){
            return true;
        }

        $request = $this->getRequest()->get($key);
        if(null !== $request){
            return true;
        }

        return $this->getFlash()->has($key);
    }

    /**
     * @param string $key
     * @return string|null
     */
    public function get($key)
    {
        $vars = $this->getLocalVars();
        if(isset($vars[$key])){
            return $vars[$key];
        }

        $matches = $this->getMatches();
        if(isset($matches[$key])){
            return $matches[$key];
        }

        $request = $this->getRequest()->get($key);
        if(null !== $request){
            return $request;
        }

        if($this->getFlash()->has($key)){
            return $this->getFlash()->get($key)[0];
        }

        return null;
    }

    /**
     * @return \string[]
     */
    public function getMatches()
    {
        return $this->getRouter()->getCurrentRoute()->getMatches();
    }

    /**
     * Getter of $router
     *
     * @return Router
     */
    private function getRouter()
    {
        return $this->router;
    }

    /**
     * Setter of $router
     *
     * @param Router $router
     */
    private function setRouter(Router $router)
    {
        $this->router = $router;
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
     * Setter of $request
     *
     * @param Request $request
     */
    private function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Getter of $flash
     *
     * @return FlashBagInterface
     */
    public function getFlash()
    {
        return $this->flash;
    }

    /**
     * Setter of $flash
     *
     * @param FlashBagInterface $flash
     */
    private function setFlash(FlashBagInterface $flash)
    {
        $this->flash = $flash;
    }

    /**
     * Getter of $localVars
     *
     * @return array
     */
    public function getLocalVars()
    {
        return $this->localVars;
    }

    /**
     * Setter of $localVars
     *
     * @param array $localVars
     */
    public function setLocalVars(array $localVars)
    {
        $this->localVars = $localVars;
    }

}
