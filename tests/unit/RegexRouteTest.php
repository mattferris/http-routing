<?php

use MattFerris\Http\Routing\RegexRoute;

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
        $route = new RegexRoute('/users/(?<user>[^/]+)/(?<action>[^/]+)', function () {});

        $matches = array();

        $this->assertTrue($route->matchUri('/users/joe/update', $matches));
        $this->assertEquals($matches['user'], 'joe');
        $this->assertEquals($matches['action'], 'update');

        // make sure we match starting from the start of the uri
        $this->assertFalse($route->matchUri('/foo/users/joe/update', $matches));

        // test required parameters
        $this->assertFalse($route->matchUri('/users/joe/'));

        // test optional parameters
        $matches = [];
        $route = new RegexRoute('/users/(?<user>[^/]+)/(?<action>[^/]+|)', function () {}, null, [], ['action' => 'update']);
        $this->assertTrue($route->matchUri('/users/joe/', $matches));
        $this->assertEquals($matches['action'], 'update');
    }

    public function testMatchMethod()
    {
        $route = new RegexRoute('/foo', function () {}, 'GET|POST');

        $this->assertTrue($route->hasMethod());
        $this->assertFalse($route->matchMethod('HEAD'));
        $this->assertTrue($route->matchMethod('GET'));
        $this->assertTrue($route->matchMethod('POST'));

        // make sure we don't match partially
        $this->assertFalse($route->matchMethod('_GET_'));
    }

    public function testMatchHeader()
    {
        $args = [];
        $route = new RegexRoute( '/foo', function () {}, null, ['Host'=>'(?<header>foo)']);

        $this->assertTrue($route->hasHeaders());
        $this->assertEquals($route->getHeaderNames(), ['host']);
        $this->assertFalse($route->matchHeader('Host', 'bar'));
        $this->assertTrue($route->matchHeader('Host', 'foo'));
        $this->assertTrue($route->matchHeader('host', 'foo', $args));
        $this->assertEquals($args['header'], 'foo');

        // make sure we don't match partially
        $this->assertFalse($route->matchHeader('host', '_foo_'));
    }

    public function testGenerateUri()
    {
        // no params
        $route = new RegexRoute('/foo', function () {});
        $this->assertEquals($route->generateUri(), '/foo');

        // (?P<foo>...) style params
        $route = new RegexRoute('/(?P<foo>.+)', function () {});
        $this->assertEquals($route->generateUri(['foo'=>'foo']), '/foo');

        // (?<foo>...) style params
        $route = new RegexRoute('/(?<foo>.+)', function () {});
        $this->assertEquals($route->generateUri(['foo'=>'foo']), '/foo');

        // (?'foo'...) style params
        $route = new RegexRoute('/(?\'foo\'.+)', function () {});
        $this->assertEquals($route->generateUri(['foo'=>'foo']), '/foo');

        // test with default params
        $route = new RegexRoute('/users/(?<user>[^/]+)/(?<action>[^/]+)', function () {}, null, [], ['action' => 'update']);
        $this->assertEquals($route->generateUri(['user' => 'joe']), '/users/joe/update');

        // test extra params are added as query string
        $this->assertEquals($route->generateUri(['user'=>'joe','action'=>'update', 'foo' => 'foo']), '/users/joe/update?foo=foo');
    }

    /**
     * @depends testGenerateUri
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage missing required parameter "foo"
     */
    public function testGenerateUriWithMissingParameter()
    {
        $route = new RegexRoute('/(?<foo>.+)', function () {});
        $route->generateUri();
    }
}

