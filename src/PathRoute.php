<?php

/**
 * HttpRouting - An HTTP routing dispatcher
 * www.bueller.ca/http-routing
 *
 * PathRoute.php
 * @copyright Copyright (c) 2015
 * @author Matt Ferris <matt@bueller.ca>
 *
 * Licensed under BSD 2-clause license
 * www.bueller.ca/http-routing/license
 */

namespace MattFerris\HttpRouting;

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
}

