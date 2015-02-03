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

    public function testGetRegexUriWithAllAlias()
    {
        $routePattern = new RoutePattern('uri/:all/end');

        $result = [];
        $this->assertSame(1, preg_match($routePattern->getRegexUri(), 'uri/123/456/end', $result));
        $this->assertSame('123/456', $result[1]);
    }

    public function testGetRegexUriWithIdAlias()
    {
        $routePattern = new RoutePattern('%id%');
        $result = [];
        $this->assertSame(1, preg_match($routePattern->getRegexUri(), 'uri/123/456', $result));
        $this->assertSame('123', $result[1]);

        $routePattern = new RoutePattern('uri/%id%');
        $result = [];
        $this->assertSame(1, preg_match($routePattern->getRegexUri(), 'uri/123/425', $result));
        $this->assertSame('123', $result[1]);

        $routePattern = new RoutePattern('uri/%id%/');
        $this->assertSame(0, preg_match($routePattern->getRegexUri(), 'uri/1a23', $result));
    }

    public function testGetRegexUriWithLangAlias()
    {
        $routePattern = new RoutePattern('uri/%lang%');

        //ISO 3166-1
        $result = [];
        $this->assertSame(1, preg_match($routePattern->getRegexUri(), 'uri/fr', $result));
        $this->assertSame('fr', $result[1]);

        //IETF language tag (but only with 2 letteres
        $result = [];
        $this->assertSame(1, preg_match($routePattern->getRegexUri(), 'uri/fr-FR', $result));
        $this->assertSame('fr-FR', $result[1]);
    }

    public function testGetRegexUriWithDateAlias()
    {
        $routePattern = new RoutePattern('uri/%date%');

        $result = [];
        $this->assertSame(1, preg_match($routePattern->getRegexUri(), 'uri/1999-02-31', $result));
        $this->assertSame('1999-02-31', $result[1]);

        $this->assertSame(0, preg_match($routePattern->getRegexUri(), 'uri/99-02-31'));

        $this->assertSame(0, preg_match($routePattern->getRegexUri(), 'uri/99-31-02'));

        $this->assertSame(0, preg_match($routePattern->getRegexUri(), 'uri/99-2-31'));
    }

    public function testGetRegexUriWithTimeAlias()
    {
        $routePattern = new RoutePattern('uri/%time%');

        $result = [];
        $this->assertSame(1, preg_match($routePattern->getRegexUri(), 'uri/16:02:59', $result));
        $this->assertSame('16:02:59', $result[1]);
    }

    public function testGetRegexUriWithDateTimeAlias()
    {
        $routePattern = new RoutePattern('uri/%datetime%');

        $result = [];
        $this->assertSame(1, preg_match($routePattern->getRegexUri(), 'uri/1999-02-31T16:02:59', $result));
        $this->assertSame('1999-02-31T16:02:59', $result[1]);

        $result = [];
        $this->assertSame(1, preg_match($routePattern->getRegexUri(), 'uri/1999-02-31T16:02:59+02:30', $result));
        $this->assertSame('1999-02-31T16:02:59+02:30', $result[1]);

        $this->assertSame(0, preg_match($routePattern->getRegexUri(), 'uri/1999-02-31 16:02:59'));

    }
}
 