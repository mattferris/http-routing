<?php

/**
 * Http Routing - An HTTP routing dispatcher
 * www.bueller.ca/http-routing
 *
 * PathRoute.php
 * @copyright Copyright (c) 2015
 * @author Matt Ferris <matt@bueller.ca>
 *
 * Licensed under BSD 2-clause license
 * www.bueller.ca/http-routing/license
 */

namespace MattFerris\Http\Routing;

class PathRoute extends RegexRoute
{
    /**
     * @see SimpleRoute::__construct()
     * @param string $uri The URI pattern
     * @param callable $action The action to dispatch the request to
     * @param string $method The HTTP method
     * @param string[string] $headers Any HTTP headers to match
     * @throws \InvalidArgumentException If $uri or $method is empty or non-string
     */
    public function __construct($uri, callable $action, $method = null, array $headers = array())
    {
        if (!is_string($uri) || empty($uri)) {
            throw new \InvalidArgumentException('$uri expects non-empty string');
        }

        $matches = array();
        if (preg_match_all('!\{([a-zA-Z_]+)\}!', $uri, $matches)) {
            $pattern = '^'.$uri;
            foreach ($matches[1] as $match) {
                $pattern = str_replace('{'.$match.'}', '(?P<'.$match.'>[^/?]+)', $pattern);
            }
            $uri = $pattern;
        }

        parent::__construct($uri, $action, $method, $headers);
    }

    /**
     * Return a URI that would match the route
     *
     * @param array $params Values for route parameters
     * @return string
     * @throw \InvalidArgumentException Required parameters haven't been
     *     specified
     */
    public function generateUri(array $params = [])
    {
        $uri = $this->uri;

        if (strpos($uri, '^') === 0) {
            $uri = substr($this->uri, 1);
        }

        $matches = [];
        if (preg_match_all('/\(\?P\<([a-zA-Z_][a-zA-Z0-9_]+)\>/', $uri, $matches)) {
            foreach ($matches[1] as $param) {
                if (!isset($params[$param])) {
                    throw new \InvalidArgumentException('missing required parameter "'.$param.'"');
                }

                $uri = preg_replace('/\(\?P\<'.$param.'\>\[\^\/\?\]\+\)/', $params[$param], $uri);
                unset($params[$param]);
            }
        }

        // add any remaining parameters as a query string
        if (count($params) > 0) {
            $qs = [];
            foreach ($params as $k => $v) {
                $qs[] = urlencode($k).'='.urlencode($v);
            }
            $uri .= '?'.implode('&', $qs);
        }

        return $uri;
    }
}

