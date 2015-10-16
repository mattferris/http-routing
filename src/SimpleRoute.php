<?php

/**
 * HttpRouting - An HTTP routing dispatcher
 * www.bueller.ca/http-routing
 *
 * AbstractRoute.php
 * @copyright Copyright (c) 2015
 * @author Matt Ferris <matt@bueller.ca>
 *
 * Licensed under BSD 2-clause license
 * www.bueller.ca/http-routing/license
 */

namespace MattFerris\HttpRouting;

class SimpleRoute implements RouteInterface
{
    /**
     * @var string The URI pattern
     */
    protected $uri;

    /**
     * @var string The HTTP method
     */
    protected $method = null;

    /**
     * @var callable The action to dispatch the request to
     */
    protected $action;

    /**
     * @var string[string] Any HTTP headers to match
     */
    protected $headers = array();

    /**
     * @param string $uri The URI pattern
     * @param callable $action The action to dispatch the request to
     * @param string $method The HTTP method
     * @param string[string] $headers Any HTTP headers to match
     * @throws \InvalidArgumentException If $uri or method is empty or non-string
     */
    public function __construct($uri, callable $action, $method = null, array $headers = array())
    {
        if (!is_string($uri) || empty($uri)) {
            throw new \InvalidArgumentException('$uri expects non-empty string');
        }

        if (!is_null($method) && (!is_string($method) || empty($method))) {
            throw new \InvalidArgumentException('$method expects non-empty string or null');
        }

        $this->uri = $uri;
        $this->action = $action;
        $this->method = $method;
        $this->headers = $headers;
    }

    /**
     * Return the URI pattern for the route
     *
     * @return string The URI pattern
     */
    public function getUri()
    {
        return $this->uri;
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
        return (strpos($uri, $this->uri) === 0);
    }

    /**
     * Check the route has HTTP method criteria
     *
     * @return bool True if method criteria exists, otherwise false
     */
    public function hasMethod()
    {
        return !is_null($this->method);
    }

    /**
     * Return the HTTP method criteria
     *
     * @return string|null The HTTP method criteria, otherwise null
     */
    public function getMethod()
    {
        return $this->method;
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
        return (strcasecmp($this->method, $method) === 0);
    }

    /**
     * Return the route action
     *
     * @return callable The route action
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Check the route has HTTP header criteria
     *
     * @return bool True if header criteria exists, otherwise false
     */
    public function hasHeaders()
    {
        return (count($this->headers) > 0);
    }

    /**
     * Return the HTTP headers that the route must match
     *
     * @return string[] The array of headers to match
     */
    public function getHeaderNames()
    {
        return array_keys($this->headers);
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
        foreach ($this->headers as $h => $v) {
            if (strcasecmp($h, $header) === 0 && strcmp($v, $value) === 0) {
                return true;
            }
        }
        return false;
    }
}

