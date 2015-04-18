<?php
namespace Puppy\Route;

/**
 * Class GroupTest
 * @package Puppy\Route
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class GroupTest extends \PHPUnit_Framework_TestCase
{

    public function testMethod()
    {
        $routePattern1 = new RoutePattern('uri1');
        $routePattern2 = new RoutePattern('uri2');

        $group = new Group([
            new IRoutePatternSetterAdapter($routePattern1),
            new IRoutePatternSetterAdapter($routePattern2),
        ]);

        $group->method('method');

        $this->assertSame('METHOD', $routePattern1->getMethod());
        $this->assertSame('METHOD', $routePattern2->getMethod());
    }

    public function testContent()
    {
        $routePattern1 = new RoutePattern('uri1');
        $routePattern2 = new RoutePattern('uri2');

        $group = new Group([
            new IRoutePatternSetterAdapter($routePattern1),
            new IRoutePatternSetterAdapter($routePattern2),
        ]);

        $group->content('content');

        $this->assertSame('content', $routePattern1->getContentType());
        $this->assertSame('content', $routePattern2->getContentType());
    }

    public function testFilter()
    {
        $routePattern1 = new RoutePattern('uri1');
        $routePattern2 = new RoutePattern('uri2');

        $group = new Group([
            new IRoutePatternSetterAdapter($routePattern1),
            new IRoutePatternSetterAdapter($routePattern2),
        ]);

        $callback = function(){};
        $group->filter($callback);

        $this->assertSame($callback, $routePattern1->getFilters()[0]);
        $this->assertSame($callback, $routePattern2->getFilters()[0]);
    }

    public function testBind()
    {
        $routePattern1 = new RoutePattern('uri1');
        $routePattern2 = new RoutePattern('uri2');

        $group = new Group([
            new IRoutePatternSetterAdapter($routePattern1),
            new IRoutePatternSetterAdapter($routePattern2),
        ]);

        $group->bind('alias', 'pattern', 'delimiter');

        $this->assertSame('(?<alias>pattern)', $routePattern1->getBindings()['delimiteralias']);
        $this->assertSame('(?<alias>pattern)', $routePattern2->getBindings()['delimiteralias']);
    }

    public function testRestrict()
    {
        $routePattern1 = new RoutePattern('uri1');
        $routePattern2 = new RoutePattern('uri2');

        $group = new Group([
            new IRoutePatternSetterAdapter($routePattern1),
            new IRoutePatternSetterAdapter($routePattern2),
        ]);

        $group->restrict('namespace');

        $this->assertSame('namespace/uri1', $routePattern1->getUri());
        $this->assertSame('namespace/uri2', $routePattern2->getUri());
    }
}
