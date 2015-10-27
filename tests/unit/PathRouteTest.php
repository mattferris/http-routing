<?php

use MattFerris\Http\Routing\PathRoute;

class PathRouteTest extends PHPUnit_Framework_TestCase
{
    public function testMatchUri()
    {
        $route = new PathRoute('/users/{user}/{action}', function () {});

        $matches = array();
        $route->matchUri('/users/joe/update?param=asdf', $matches);

        $this->assertEquals($matches['user'], 'joe');
        $this->assertEquals($matches['action'], 'update');
    }

    public function testGenerateUri()
    {
        $route = new PathRoute('/users', function () {});
        $this->assertEquals($route->generateUri(), '/users');

        $route = new PathRoute('/users/{user}/{action}', function () {});
        $uri = $route->generateUri(['user' => 'joe', 'action' => 'update']);
        $this->assertEquals($uri, '/users/joe/update');
    }

    /**
     * @depends testGenerateUri
     */
    public function testGenerateUriWithMissingParams()
    {
        $this->setExpectedException('InvalidArgumentException', 'required named route parameter "action" not specified');
        $route = new PathRoute('/users/{user}/{action}', function () {});
        $route->generateUri(['user' => 'joe']);
    }
}

