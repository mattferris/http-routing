<?php

/*
 * chickadee demo project
 */

namespace mattferris\chickadee\Demo;

use mattferris\chickadee\Request;

class UsersController extends \mattferris\chickadee\AbstractController
{
    public function getAction(Request $request)
    {
        return $this->response(array(
            'href' => $request->getUri(),
            'users' => array(
                'jane' => array(
                    'href' => '/users/jane'
                ),
                'joe' => array(
                    'href' => '/users/joe'
                ),
                'john' => array(
                    'href' => '/users/john'
                ),
            )
        ));
    }

    public function getUserAction(Request $request, $user)
    {
        return $this->response(array(
            'href' => $request->getUri(),
            'user' => $user
        ));
    }
}

