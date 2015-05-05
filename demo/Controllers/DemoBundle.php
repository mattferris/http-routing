<?php

/*
 * chickadee demo site
 */

namespace mattferris\chickadee\Demo;

use mattferris\chickadee\BundleInterface;

class DemoBundle implements BundleInterface
{
    public function provides()
    {
        return array(
            array(
                'method' => 'GET',
                'pattern' => '#^/$#',
                'action' => 'mattferris\\chickadee\\Demo\\DefaultController:getAction'
            ),
            array(
                'method' => 'GET',
                'pattern' => '#^/users$#',
                'action' => 'mattferris\\chickadee\\Demo\\UsersController:getAction'
            ),
            array(
                'method' => 'GET',
                'pattern' => '#^/users/(?P<user>.*)$#',
                'action' => 'mattferris\\chickadee\\Demo\\UsersController:getUserAction'
            ),
/*
            array(
                'method' => 'PUT',
                'pattern' => '#^/users$#',
                'action' => 'mattferris\\chickadee\\Demo\\UsersController:putAction'
            ),
*/
            array(
                'method' => 'GET',
                'pattern' => '#.*#',
                'action' => 'mattferris\\chickadee\\Demo\\ErrorController:error404Action'
            )
        );
    }
}

