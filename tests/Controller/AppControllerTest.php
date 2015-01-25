<?php
namespace Puppy\Controller;

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

        $appController = new AppController(new \ArrayObject(['template' => $template]));
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
}
