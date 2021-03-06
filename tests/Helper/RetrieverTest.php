<?php
namespace Puppy\Helper;

use Pimple\Container;
use Puppy\Route\Route;
use Puppy\Route\RouteFinder;
use Puppy\Route\RoutePattern;
use Puppy\Route\Router;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;

/**
 * Class RetrieverTest
 * @package Puppy\Helper
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class RetrieverTest extends \PHPUnit_Framework_TestCase
{

    public function testGetDefault()
    {
        $retriever = $this->provideRetriever(false, false, false);
        $this->assertNull($retriever->get('all', 'default'));
    }

    public function testRetrieveArgs()
    {
        $retriever = $this->provideRetriever(true, true, true);
        $this->assertSame('REQUEST_URI', $retriever->get('all', 'default'));
    }

    public function testRetrieveRequest()
    {
        $retriever = $this->provideRetriever(false, true, true);
        $this->assertSame('$_POST', $retriever->get('all', 'default'));
    }

    public function testRetrieveFlash()
    {
        $retriever = $this->provideRetriever(false, false, true);
        $this->assertSame('flash', $retriever->get('all', 'default'));
    }

    public function testGetLocalVars()
    {
        $retriever = $this->provideRetriever(true, true, true);
        $retriever->setLocalVars(['all' => 'local']);
        $this->assertSame('local', $retriever->get('all', 'default'));
    }

    public function testHasLocalVarsTrue()
    {
        $retriever = $this->provideRetriever(false, false, false);
        $retriever->setLocalVars(['all' => 'local']);
        $this->assertTrue($retriever->has('all'));
    }

    public function testHasLocalVarsFalse()
    {
        $retriever = $this->provideRetriever(false, false, false);
        $this->assertFalse($retriever->has('all'));
    }

    /**
     * @param $isInArgs
     * @param Container $services
     * @return Router
     */
    private function provideRouter($isInArgs, Container $services)
    {
        $router = new Router(new RouteFinder($services));
        $router->addRoute(
            new Route(
                new RoutePattern($isInArgs ? ':all' : 'REQUEST_URI'), function () {}
            )
        );
        $router->find(new Request([], [], [], [], [], ['REQUEST_URI' => 'REQUEST_URI']));
        return $router;
    }

    /**
     * @param $isInRequest
     * @return RequestStack
     */
    private function provideRequestStack($isInRequest)
    {
        $requestStack = new RequestStack();
        $requestStack->push($this->provideRequest($isInRequest));
        return $requestStack;
    }

    /**
     * @param $isInRequest
     * @return Request
     */
    private function provideRequest($isInRequest)
    {
        if($isInRequest){
            return new Request([], ['all' => '$_POST']);
        }
        return new Request();
    }

    /**
     * @param $isInFlash
     * @return FlashBag
     */
    private function provideFlashBag($isInFlash)
    {
        $flashBag = new FlashBag();
        if ($isInFlash) {
            $flashBag->add('all', 'flash');
        }
        return $flashBag;
    }

    /**
     * @param $isInArgs
     * @param $isInRequest
     * @param $isInFlash
     * @return Retriever
     */
    private function provideRetriever($isInArgs, $isInRequest, $isInFlash)
    {
        return new Retriever(
            $this->provideRouter($isInArgs, new Container()),
            $this->provideRequestStack($isInRequest),
            $this->provideFlashBag($isInFlash)
        );
    }
}
