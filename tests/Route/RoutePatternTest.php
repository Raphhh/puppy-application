<?php
namespace Puppy\Route;

/**
 * Class RoutePatternTest
 * @package Puppy\Route
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class RoutePatternTest extends \PHPUnit_Framework_TestCase
{

    /**
     *
     */
    public function testSetMethod()
    {
        $routePattern = new RoutePattern('');
        $routePattern->setMethod('get');
        $this->assertSame('GET', $routePattern->getMethod());
    }

    public function testGetRegexUri()
    {
        $routePattern = new RoutePattern('uri');
        $this->assertSame('#uri#', $routePattern->getRegexUri());
    }
}
 