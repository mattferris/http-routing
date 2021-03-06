<?php

use MattFerris\Http\Routing\SimpleRoute;

class SimpleRouteTest extends PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $route = new SimpleRoute('/foo', function () {});
        $this->assertFalse($route->hasMethod());
        $this->assertFalse($route->hasHeaders());
    }

    /**
     * @depends testConstruct
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage $action expects callable or "class:method"
     */
    public function testConstructWithBadAction()
    {
        $route = new SimpleRoute('/foo', 'foo');
    }

    /**
     * @depends testConstruct
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage $action doesn't exist
     */
    public function testConstructWithNonExistentAction()
    {
        $route = new SimpleRoute('/foo', 'Foo:bar');
    }

    public function testMatchUri()
    {
        $route = new SimpleRoute('/users', function () {});
        $this->assertTrue($route->matchUri('/users/joe/update?param=asdf'));
    }

    public function testMatchMethod()
    {
        $route = new SimpleRoute('/foo', function () {}, 'GET');

        $this->assertTrue($route->hasMethod());
        $this->assertFalse($route->matchMethod('HEAD'));
        $this->assertTrue($route->matchMethod('GET'));
    }

    public function testMatchHeader()
    {
        $route = new SimpleRoute('/foo', function () {}, null, array('Host'=>'foo'));

        $this->assertTrue($route->hasHeaders());
        $this->assertEquals($route->getHeaderNames(), array('host'));
        $this->assertFalse($route->matchHeader('Host', 'bar'));
        $this->assertTrue($route->matchHeader('Host', 'foo'));
        $this->assertTrue($route->matchHeader('host', 'foo'));
    }

    public function testGenerateUri()
    {
        $route = new SimpleRoute('/foo', function () {});
        $this->assertEquals($route->generateUri(), '/foo');

        // test extra parameters added as query string
        $this->assertEquals($route->generateUri(['bar'=>'baz']), '/foo?bar=baz');
    }
}

