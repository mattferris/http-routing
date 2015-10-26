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
}

