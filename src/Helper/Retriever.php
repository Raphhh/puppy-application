<?php
namespace Puppy\Helper;

use Puppy\Route\Router;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

/**
 * Class Retriever
 * retrieves params given by different way between controllers and views.
 *
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
     * @var RequestStack
     */
    private $requestStack;

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
     * @param RequestStack $requestStack
     * @param FlashBagInterface $flashBag
     */
    public function __construct(Router $router, RequestStack $requestStack, FlashBagInterface $flashBag)
    {
        $this->setRouter($router);
        $this->setRequestStack($requestStack);
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

        $request = $this->getRequestStack()->getCurrentRequest()->get($key);
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

        $request = $this->getRequestStack()->getCurrentRequest()->get($key);
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
     * Getter of $requestStack
     *
     * @return RequestStack
     */
    public function getRequestStack()
    {
        return $this->requestStack;
    }

    /**
     * Setter of $requestStack
     *
     * @param RequestStack $requestStack
     */
    private function setRequestStack(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
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
