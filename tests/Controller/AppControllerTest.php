<?php
namespace Puppy\Controller;

use Puppy\Helper\Retriever;
use Puppy\Route\Route;
use Puppy\Route\RouteFinder;
use Puppy\Route\RoutePattern;
use Puppy\Route\Router;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;

/**
 * Class AppControllerTest
 * @package Puppy
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class AppControllerTest extends \PHPUnit_Framework_TestCase
{

    public function testRender()
    {
        $templateFile = 'template-file.twig';
        $vars = ['var' => 'value'];

        $template = $this->getMockBuilder('Puppy\resources\TemplateMock')->getMock();
        $template->expects($this->once())
            ->method('render')
            ->with($templateFile, $vars)
            ->will($this->returnValue('render result'));

        $retriever = $this->getMockBuilder('Puppy\Helper\Retriever')->disableOriginalConstructor()->getMock();
        $retriever->expects($this->once())
            ->method('setLocalVars')
            ->with($vars);

        $appController = new AppController(new \ArrayObject(['template' => $template, 'retriever' => $retriever]));
        $this->assertSame('render result', $appController->render($templateFile, $vars));
    }

    public function testRedirect()
    {
        $appController = new AppController();
        $result = $appController->redirect('url', 303, ['key' => 'value']);
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $result);
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $result);
        $this->assertSame('url', $result->getTargetUrl());
        $this->assertSame(303, $result->getStatusCode());
        $this->assertSame('value', $result->headers->get('key'));
    }

    public function testRedirectWithDefaultStatus()
    {
        $appController = new AppController();
        $result = $appController->redirect('url');
        $this->assertSame(302, $result->getStatusCode());
    }

    public function testError404()
    {
        $appController = new AppController();
        $result = $appController->error404();
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $result);
        $this->assertSame(404, $result->getStatusCode());
    }

    public function testFlash()
    {
        $session = $this->getMockBuilder('Puppy\resources\SessionMock')->getMock();
        $session->expects($this->once())
            ->method('getFlashBag')
            ->will($this->returnValue('getFlashBag result'));

        $appController = new AppController(new \ArrayObject(['session' => $session]));
        $this->assertSame('getFlashBag result', $appController->flash());
    }

    public function testGetService()
    {
        $appController = new AppController(new \ArrayObject(['key' => 'service']));
        $this->assertSame('service', $appController->getService('key'));
    }

    public function testGetServiceNotSet()
    {
        $appController = new AppController(new \ArrayObject());

        $this->setExpectedException('InvalidArgumentException', 'Service key not found');
        $appController->getService('key');
    }

    public function testRetrieveDefault()
    {
        $appController = new AppController($this->provideServices(false, false, false));
        $this->assertSame('default', $appController->retrieve('all', 'default'));
    }

    public function testRetrieveArgs()
    {
        $appController = new AppController($this->provideServices(true, true, true));
        $this->assertSame('REQUEST_URI', $appController->retrieve('all', 'default'));
    }

    public function testRetrieveRequest()
    {
        $appController = new AppController($this->provideServices(false, true, true));
        $this->assertSame('$_POST', $appController->retrieve('all', 'default'));
    }

    public function testRetrieveFlash()
    {
        $appController = new AppController($this->provideServices(false, false, true));
        $this->assertSame('flash', $appController->retrieve('all', 'default'));
    }

    /**
     * @param $isInArgs
     * @param \ArrayAccess $services
     * @return Router
     */
    private function provideRouter($isInArgs, \ArrayAccess $services)
    {
        $router = new Router(new RouteFinder());
        $router->addRoute(
            new Route(
                new RoutePattern($isInArgs ? ':all' : 'REQUEST_URI'), function () {}
            )
        );
        $router->find(new Request([], [], [], [], [], ['REQUEST_URI' => 'REQUEST_URI']), $services);
        return $router;
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
     * @return \ArrayObject
     */
    private function provideServices($isInArgs, $isInRequest, $isInFlash)
    {
        $services = new \ArrayObject();
        $services['retriever'] = new Retriever(
            $this->provideRouter($isInArgs, new \ArrayObject()),
            $this->provideRequest($isInRequest),
            $this->provideFlashBag($isInFlash)
        );
        return $services;
    }
}
