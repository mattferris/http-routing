<?php

/**
 * Http Routing - An HTTP routing dispatcher
 * www.bueller.ca/http-routing
 *
 * RouteInterface.php
 * @copyright Copyright (c) 2015
 * @author Matt Ferris <matt@bueller.ca>
 *
 * Licensed under BSD 2-clause license
 * www.bueller.ca/http-routing/license
 */

namespace MattFerris\Http\Routing;

interface RouteInterface
{
    /**
     * Return the URI pattern for the route
     *
     * @return string The URI pattern
     */
    public function getUri();

    /**
     * Match the supplied against the routes URI
     *
     * @param string $uri The URI to match
     * @param array &$matches Any partial URI matches
     * @return bool True if URI matches, otherwise false
     * @throws \InvalidArgumentException If $uri isn't a string
     */
    public function matchUri($uri, array &$matches = array());

    /**
     * Check the route has HTTP method criteria
     *
     * @return bool True if method criteria exists, otherwise false
     */
    public function hasMethod();

    /**
     * Return the HTTP method criteria
     *
     * @return string|null The HTTP method criteria, otherwise null
     */
    public function getMethod();

    /**
     * Match the supplied HTTP method against the route's method, match should
     * be case-insensitive
     *
     * @param string The HTTP method to match
     * @param array &$matches Any partial URI matches
     * @return bool True if method matches, otherwise false
     * @throws \InvalidArgumentException If $method isn't a string
     */
    public function matchMethod($method, array &$matches = array());

    /**
     * Return the route action
     *
     * @return callable The route action
     */
    public function getAction();

    /**
     * Check the route has HTTP header criteria
     *
     * @return bool True if header criteria exists, otherwise false
     */
    public function hasHeaders();

    /**
     * Return the HTTP headers that the route must match
     *
     * @return string[] The array of headers to match
     */
    public function getHeaderNames();

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
    public function matchHeader($header, $value, array &$matches = array());
}

