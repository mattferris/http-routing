<?php

/**
 * Http Routing - An HTTP routing dispatcher
 * www.bueller.ca/http-routing
 *
 * RegexRoute.php
 * @copyright Copyright (c) 2015
 * @author Matt Ferris <matt@bueller.ca>
 *
 * Licensed under BSD 2-clause license
 * www.bueller.ca/http-routing/license
 */

namespace MattFerris\Http\Routing;

class RegexRoute extends SimpleRoute
{
    /**
     * Return regular expression to match parameters
     *
     * @param string $param The parameter to match
     * @return string
     */
    protected function generateParamRegex($param)
    {
        if (!is_string($param) || empty($param)) {
            throw new \InvalidArgumentException('$param expects non-empty string');
        }

        $syntaxA = '\(\?P\<'.$param.'\>[^\)]+?\)';
        $syntaxB = '\(\?\<'.$param.'\>[^\)]+?\)';
        $syntaxC = '\(\?\''.$param.'\'[^\)]+?\)';

        return '/'.$syntaxA.'|'.$syntaxB.'|'.$syntaxC.'/';
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

        $matches = [];
        $pattern = $this->generateParamRegex('([a-zA-Z_][a-zA-Z0-9_]+)');
        if (preg_match_all($pattern, $uri, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {

                /*
                 * $matches is an array containing arrays for each $match in the
                 * $uri. Each $match contains the values of the matches for the
                 * entire $uri and each sub-pattern. If a sub-pattern didn't
                 * match anything, it's array index contains an empty string. To
                 * find the name of the parameter, we need to loop through the
                 * match, skipping the first index ($uri), and test for a
                 * non-empty string. The first non-empty string found is the
                 * name of the parameter.
                 */
                array_shift($match); // skip the first value
                $param = null;
                foreach ($match as $value) {
                    if (!empty($value)) {
                        $param = $value;
                        break;
                    }
                }

                if (!isset($params[$param])) {
                    throw new \InvalidArgumentException('missing required parameter "'.$param.'"');
                }

                $pattern = $this->generateParamRegex($param);
                $uri = preg_replace($pattern, $params[$param], $uri);
            }
        }

        return $uri;
    }

    /**
     * Match the supplied against the routes URI
     *
     * @param string $uri The URI to match
     * @param array &$matches Any partial URI matches
     * @return bool True if URI matches, otherwise false
     * @throws \InvalidArgumentException If $uri isn't a string
     */
    public function matchUri($uri, array &$matches = array())
    {
        return (bool)preg_match('!^'.$this->uri.'!', $uri, $matches);
    }

    /**
     * Match the supplied HTTP method against the route's method, match should
     * be case-insensitive
     *
     * @param string The HTTP method to match
     * @param array &$matches Any partial URI matches
     * @return bool True if method matches, otherwise false
     * @throws \InvalidArgumentException If $method isn't a string
     */
    public function matchMethod($method, array &$matches = array())
    {
        return (bool)preg_match('!^'.$this->method.'$!', $method, $matches);
    }

    /**
     * Match the supplied $value for $header against the route's criteria. The
     * header names are case-insensitive.
     *
     * @param string $header The HTTP header to match
     * @param mixed $value The value of the HTTP header
     * @param array &$matches Any partial URI matches
     * @return bool True if header matches, otherwise false
     * @throws \InvalidArgumentException If $header isn't a string
     */
    public function matchHeader($header, $value, array &$matches = array())
    {
        // normalize
        $header = strtolower($header);
        if (isset($this->headers[$header]) && preg_match('!^'.$this->headers[$header].'$!', $value, $matches)) {
            return true;
        }
        return false;
    }
}

