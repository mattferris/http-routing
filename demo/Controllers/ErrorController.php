<?php

/*
 * chickadee demo project
 */

namespace mattferris\chickadee\Demo;

use mattferris\chickadee\Request;

class ErrorController extends \mattferris\chickadee\AbstractController
{
    public function error404Action(Request $request)
    {
        return $this->response(array(
            'href' => $request->getUri(),
            'error' => '404 not found'
        ), array(), 404);
    }
}

