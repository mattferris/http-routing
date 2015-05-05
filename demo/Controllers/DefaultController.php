<?php

/*
 * chickadee demo project
 */

namespace mattferris\chickadee\Demo;

class DefaultController extends \mattferris\chickadee\AbstractController
{
    public function getAction(Request $request)
    {
        return $this->response(array(
            'href' => $request->getUri(),
            'related' => array(
                'users' => array(
                    'href' => '/users'
                )
            )
        ));
    }
}

