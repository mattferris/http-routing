<?php

use MattFerris\Http\Routing\PathRoute;

class PathRouteTest extends PHPUnit_Framework_TestCase
{
    public function testMatchUri()
    {
        $route = new PathRoute('/users/{user}/{action}', function () {});

        $matches = [];
        $route->matchUri('/users/joe/update', $matches);

        $this->assertEquals($matches['user'], 'joe');
        $this->assertEquals($matches['action'], 'update');

        // test required parameters
        $this->assertFalse($route->matchUri('/users/joe/'));

        // test optional parameters
        $matches = [];
        $route = new PathRoute('/users/{user}/{action}', function () {}, null, [], ['action' => 'update']);
        $this->assertTrue($route->matchUri('/users/joe/', $matches));
        $this->assertEquals($matches['action'], 'update');
    }

    public function testGenerateUri()
    {
        $route = new PathRoute('/users', function () {});
        $this->assertEquals($route->generateUri(), '/users');

        // test with supplied parameters
        $route = new PathRoute('/users/{user}/{action}', function () {});
        $uri = $route->generateUri(['user' => 'joe', 'action' => 'update']);
        $this->assertEquals($uri, '/users/joe/update');

        // test with default parameters
        $route = new PathRoute('/users/{user}/{action}', function () {}, null, [], ['action' => 'update']);
        $uri = $route->generateUri(['user' => 'joe']);
        $this->assertEquals($uri, '/users/joe/update');

        // test extra params are added as query string
        $this->assertEquals($route->generateUri(['user'=>'joe','action'=>'update','bar'=>'baz']), '/users/joe/update?bar=baz');
    }

    /**
     * @depends testGenerateUri
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage missing required parameter "foo"
     */
    public function testGenerateUriWithMissingParams()
    {
        $route = new PathRoute('/{foo}', function () {});
        $route->generateUri();
    }
}

