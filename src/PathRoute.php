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
     * @param array[string] $defaults Default parameter values
     * @throws \InvalidArgumentException If $uri or $method is empty or non-string
     */
    public function __construct($uri, callable $action, $method = null, array $headers = [], array $defaults = [])
    {
        if (!is_string($uri) || empty($uri)) {
            throw new \InvalidArgumentException('$uri expects non-empty string');
        }

        $matches = array();
        if (preg_match_all('!\{([a-zA-Z_]+)\}!', $uri, $matches)) {
            $optionalParam = false;
            $pattern = '^'.$uri;
            foreach ($matches[1] as $match) {
                $paramPattern = '(?<'.$match.'>[^/]+)';

                /*
                 * If a default value has been provided for the parameter, then
                 * it is considered optional, and the parameters regex needs to
                 * allow for an empty match. However, once an optional parameter
                 * has been defined, all remaining parameters must also be
                 * optional.
                 */
                if (isset($defaults[$match]) && !$optionalParam) {
                    $paramPattern = '(?<'.$match.'>[^/]+|)';
                    $optionalParam = true;
                } elseif ($optionalParam && !isset($defaults[$match])) {
                    throw new \BadLogicException('can\'t define required parameter "'.$match.'" once an optional parameter has been defined');
                }

                $pattern = str_replace('{'.$match.'}', $paramPattern, $pattern);
            }
            $uri = $pattern;
        }

        parent::__construct($uri, $action, $method, $headers, $defaults);
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
        if (preg_match_all('/\(\?\<([a-zA-Z_][a-zA-Z0-9_]+)\>/', $uri, $matches)) {

            // prefill default values
            foreach ($this->defaults as $k => $v) {
                if (!isset($params[$k])) {
                    $params[$k] = $v;
                }
            }

            // replace params in the URI
            foreach ($matches[1] as $param) {
                if (!isset($params[$param])) {
                    throw new \InvalidArgumentException('missing required parameter "'.$param.'"');
                }

                $uri = preg_replace('/\(\?\<'.$param.'\>\[\^\/\]\+(\)|\|\))/', $params[$param], $uri);
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

