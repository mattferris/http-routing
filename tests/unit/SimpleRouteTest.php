<?php

use MattFerris\HttpRouting\SimpleRoute;

class SimpleRouteTest extends PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $route = new SimpleRoute('/foo', function () {});
        $this->assertFalse($route->hasMethod());
        $this->assertFalse($route->hasHeaders());
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
        $this->assertEquals($route->getHeaderNames(), array('Host'));
        $this->assertFalse($route->matchHeader('Host', 'bar'));
        $this->assertTrue($route->matchHeader('Host', 'foo'));
        $this->assertTrue($route->matchHeader('host', 'foo'));
    }
}

