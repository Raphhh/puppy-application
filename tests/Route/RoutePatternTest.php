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
        $routePattern = new RoutePattern(':all');

        $result = [];
        $this->assertSame(1, preg_match($routePattern->getRegexUri(), 'uri/123/456/end', $result));
        $this->assertSame('uri/123/456/end', $result[1]);
        $this->assertSame('uri/123/456/end', $result['all']);

        $routePattern = new RoutePattern('uri/:all/end');

        $result = [];
        $this->assertSame(1, preg_match($routePattern->getRegexUri(), 'uri/123/456/end', $result));
        $this->assertSame('123/456', $result[1]);
        $this->assertSame('123/456', $result['all']);
    }

    public function testGetRegexUriWithHomeAlias()
    {
        $routePattern = new RoutePattern(':home');

        $result = [];
        $this->assertSame(1, preg_match($routePattern->getRegexUri(), '', $result));
        $this->assertSame('', $result[1]);
        $this->assertSame('', $result['home']);

        $result = [];
        $this->assertSame(1, preg_match($routePattern->getRegexUri(), '/', $result));
        $this->assertSame('/', $result[1]);
        $this->assertSame('/', $result['home']);

        $this->assertSame(0, preg_match($routePattern->getRegexUri(), '/index.html'));
    }

    public function testGetRegexUriWithIdAlias()
    {
        $routePattern = new RoutePattern(':id');
        $result = [];
        $this->assertSame(1, preg_match($routePattern->getRegexUri(), 'uri/123/456', $result));
        $this->assertSame('123', $result[1]);
        $this->assertSame('123', $result['id']);

        $routePattern = new RoutePattern('uri/:id');
        $result = [];
        $this->assertSame(1, preg_match($routePattern->getRegexUri(), 'uri/123/425', $result));
        $this->assertSame('123', $result[1]);
        $this->assertSame('123', $result['id']);

        $routePattern = new RoutePattern(':id');
        $this->assertSame(0, preg_match($routePattern->getRegexUri(), '0'));

        $result = [];
        $routePattern = new RoutePattern(':id');
        $this->assertSame(1, preg_match($routePattern->getRegexUri(), '01', $result));
        $this->assertSame('1', $result[1]);
        $this->assertSame('1', $result['id']);

        $result = [];
        $routePattern = new RoutePattern(':id');
        $this->assertSame(1, preg_match($routePattern->getRegexUri(), '10', $result));
        $this->assertSame('10', $result[1]);
        $this->assertSame('10', $result['id']);

        $result = [];
        $routePattern = new RoutePattern(':id');
        $this->assertSame(1, preg_match($routePattern->getRegexUri(), '1.23', $result));
        $this->assertSame('1', $result[1]);
        $this->assertSame('1', $result['id']);
    }

    public function testGetRegexUriWithIndexAlias()
    {
        $result = [];
        $routePattern = new RoutePattern(':index');
        $this->assertSame(1, preg_match($routePattern->getRegexUri(), 'uri/123/456', $result));
        $this->assertSame('123', $result[1]);
        $this->assertSame('123', $result['index']);

        $result = [];
        $routePattern = new RoutePattern('uri/:index');
        $this->assertSame(1, preg_match($routePattern->getRegexUri(), 'uri/123/425', $result));
        $this->assertSame('123', $result[1]);
        $this->assertSame('123', $result['index']);

        $result = [];
        $routePattern = new RoutePattern(':index');
        $this->assertSame(1, preg_match($routePattern->getRegexUri(), '0', $result));
        $this->assertSame('0', $result[1]);
        $this->assertSame('0', $result['index']);

        $result = [];
        $routePattern = new RoutePattern(':index');
        $this->assertSame(1, preg_match($routePattern->getRegexUri(), '01', $result));
        $this->assertSame('01', $result[1]);
        $this->assertSame('01', $result['index']);

        $result = [];
        $routePattern = new RoutePattern(':index');
        $this->assertSame(1, preg_match($routePattern->getRegexUri(), '10', $result));
        $this->assertSame('10', $result[1]);
        $this->assertSame('10', $result['index']);

        $result = [];
        $routePattern = new RoutePattern(':index');
        $this->assertSame(1, preg_match($routePattern->getRegexUri(), '1.23', $result));
        $this->assertSame('1', $result[1]);
        $this->assertSame('1', $result['index']);
    }

    public function testGetRegexUriWithLangAlias()
    {
        $routePattern = new RoutePattern('uri/:lang');

        //ISO 3166-1
        $result = [];
        $this->assertSame(1, preg_match($routePattern->getRegexUri(), 'uri/fr', $result));
        $this->assertSame('fr', $result[1]);
        $this->assertSame('fr', $result['lang']);

        //IETF language tag (but only with 2 letteres
        $result = [];
        $this->assertSame(1, preg_match($routePattern->getRegexUri(), 'uri/fr-FR', $result));
        $this->assertSame('fr-FR', $result[1]);
        $this->assertSame('fr-FR', $result['lang']);
    }

    public function testGetRegexUriWithDateAlias()
    {
        $routePattern = new RoutePattern('uri/:date');

        $result = [];
        $this->assertSame(1, preg_match($routePattern->getRegexUri(), 'uri/1999-02-31', $result));
        $this->assertSame('1999-02-31', $result[1]);
        $this->assertSame('1999-02-31', $result['date']);

        $this->assertSame(0, preg_match($routePattern->getRegexUri(), 'uri/99-02-31'));

        $this->assertSame(0, preg_match($routePattern->getRegexUri(), 'uri/99-31-02'));

        $this->assertSame(0, preg_match($routePattern->getRegexUri(), 'uri/99-2-31'));
    }

    public function testGetRegexUriWithTimeAlias()
    {
        $routePattern = new RoutePattern('uri/:time');

        $result = [];
        $this->assertSame(1, preg_match($routePattern->getRegexUri(), 'uri/16:02:59', $result));
        $this->assertSame('16:02:59', $result[1]);
        $this->assertSame('16:02:59', $result['time']);
    }

    public function testGetRegexUriWithDateTimeAlias()
    {
        $routePattern = new RoutePattern('uri/:datetime');

        $result = [];
        $this->assertSame(1, preg_match($routePattern->getRegexUri(), 'uri/1999-02-31T16:02:59', $result));
        $this->assertSame('1999-02-31T16:02:59', $result[1]);
        $this->assertSame('1999-02-31T16:02:59', $result['datetime']);

        $result = [];
        $this->assertSame(1, preg_match($routePattern->getRegexUri(), 'uri/1999-02-31T16:02:59+02:30', $result));
        $this->assertSame('1999-02-31T16:02:59+02:30', $result[1]);
        $this->assertSame('1999-02-31T16:02:59+02:30', $result['datetime']);

        $this->assertSame(0, preg_match($routePattern->getRegexUri(), 'uri/1999-02-31 16:02:59'));

    }

    public function testAddAlias()
    {
        $routePattern = new RoutePattern('uri');
        $this->assertSame('(?<all>.*)', $routePattern->getAlias()[':all']);

        $routePattern->addAlias('all', 'new pattern');
        $this->assertSame('(?<all>new pattern)', $routePattern->getAlias()[':all']);
    }

    public function testGetRegexUriWithOverriddenAlias()
    {
        $routePattern = new RoutePattern(':id');
        $routePattern->addAlias('id', '[a-z]+');

        $result = [];
        $this->assertSame(1, preg_match($routePattern->getRegexUri(), 'uri', $result));
        $this->assertSame('uri', $result[1]);
        $this->assertSame('uri', $result['id']);
    }
}
 