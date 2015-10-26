<?php

use MattFerris\HttpRouting\RegexRoute;

class RegexRouteTest extends PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $route = new RegexRoute('/foo', function () {});
        $this->assertFalse($route->hasMethod());
        $this->assertFalse($route->hasHeaders());
    }

    public function testMatchUri()
    {
        $route = new RegexRoute('^/users/(?P<user>[^/?]+)/(?P<action>[^/?]+)', function () {});

        $matches = array();

        $this->assertTrue($route->matchUri('/users/joe/update?param=asdf', $matches));
        $this->assertEquals($matches['user'], 'joe');
        $this->assertEquals($matches['action'], 'update');
    }

    public function testMatchMethod()
    {
        $route = new RegexRoute('^/foo', function () {}, 'GET|POST');

        $this->assertTrue($route->hasMethod());
        $this->assertFalse($route->matchMethod('HEAD'));
        $this->assertTrue($route->matchMethod('GET'));
        $this->assertTrue($route->matchMethod('POST'));
    }

    public function testMatchHeader()
    {
        $args = [];
        $route = new RegexRoute( '^/foo', function () {}, null, ['Host'=>'(?P<header>foo)']);

        $this->assertTrue($route->hasHeaders());
        $this->assertEquals($route->getHeaderNames(), ['host']);
        $this->assertFalse($route->matchHeader('Host', 'bar'));
        $this->assertTrue($route->matchHeader('Host', 'foo'));
        $this->assertTrue($route->matchHeader('host', 'foo', $args));
        $this->assertEquals($args['header'], 'foo');
    }
}

