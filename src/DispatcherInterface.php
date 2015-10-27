<?php

/**
 * Http Routing - An HTTP routing dispatcher
 * www.bueller.ca/http-routing
 *
 * DispatcherInterface.php
 * @copyright Copyright (c) 2015
 * @author Matt Ferris <matt@bueller.ca>
 *
 * Licensed under BS 2-clause license
 * www.bueller.ca/http-routing/license
 */

namespace MattFerris\Http\Routing; 

use Psr\Http\Message\ServerRequestInterface;

interface DispatcherInterface
{
    /**
     * Add a route object to the dispatcher
     *
     * @param \MattFerris\Http\Routing\RouteInterface $route The route to add
     * @param string $name The name of the route to get the URI for
     * @return self
     */
    public function add(RouteInterface $route, $name = null);

    /**
     * Insert a route object at a specific array index
     *
     * @param \MattFerris\Http\Routing\RouteInterface $route The route to add
     * @param int $position The array index to insert the route in
     * @param string $name The name of the route to get the URI for
     * @return self
     * @throws \InvalidArgumentException If $position doesn't exist
     */
    public function insert(RouteInterface $route, $position, $name = null);

    /**
     * Add a route by supplying all the parameters
     *
     * @param string $uri The URI to match
     * @param callable $action The action to dispatch the request to
     * @param string[string] $headers Any HTTP headers to match
     * @param string $method The HTTP method to match
     * @param string $name The name of the route to get the URI for
     * @return self
     */
    public function route($uri, callable $action, $method, array $headers, $name = null);

    /**
     * Add a route to match any HTTP method
     *
     * @param string $uri The URI to match
     * @param callable $action The action to dispatch the request to
     * @param string[string] $headers Any HTTP headers to match
     * @param string $name The name of the route to get the URI for
     * @return self
     */
    public function any($uri, callable $action, array $headers = [], $name = null);

    /**
     * Add a route to match an HTTP GET request
     *
     * @param string $uri The URI to match
     * @param callable $action The action to dispatch the request to
     * @param string[string] $headers Any HTTP headers to match
     * @param string $name The name of the route to get the URI for
     * @return self
     */
    public function get($uri, callable $action, array $headers = [], $name = null);

    /**
     * Add a route to match an HTTP POST request
     *
     * @param string $uri The URI to match
     * @param callable $action The action to dispatch the request to
     * @param string[string] $headers Any HTTP headers to match
     * @param string $name The name of the route to get the URI for
     * @return self
     */
    public function post($uri, callable $action, array $headers = [], $name = null);

    /**
     * Add a route to match an HTTP PUT request
     *
     * @param string $uri The URI to match
     * @param callable $action The action to dispatch the request to
     * @param string[string] $headers Any HTTP headers to match
     * @param string $name The name of the route to get the URI for
     * @return self
     */
    public function put($uri, callable $action, array $headers = [], $name = null);

    /**
     * Add a route to match an HTTP DELETE request
     *
     * @param string $uri The URI to match
     * @param callable $action The action to dispatch the request to
     * @param string[string] $headers Any HTTP headers to match
     * @param string $name The name of the route to get the URI for
     * @return self
     */
    public function delete($uri, callable $action, array $headers = [], $name = null);

    /**
     * Add a route to match an HTTP HEAD request
     *
     * @param string $uri The URI to match
     * @param callable $action The action to dispatch the request to
     * @param string[string] $headers Any HTTP headers to match
     * @param string $name The name of the route to get the URI for
     * @return self
     */
    public function head($uri, callable $action, array $headers = [], $name = null);

    /**
     * Add a route to match an HTTP OPTIONS request
     *
     * @param string $uri The URI to match
     * @param callable $action The action to dispatch the request to
     * @param string[string] $headers Any HTTP headers to match
     * @param string $name The name of the route to get the URI for
     * @return self
     */
    public function options($uri, callable $action, array $headers = [], $name = null);

    /**
     * Add a route to match an HTTP TRACE request
     *
     * @param string $uri The URI to match
     * @param callable $action The action to dispatch the request to
     * @param string[string] $headers Any HTTP headers to match
     * @param string $name The name of the route to get the URI for
     * @return self
     */
    public function trace($uri, callable $action, array $headers = [], $name = null);

    /**
     * Get a matching URI for a named route
     *
     * @param string $name The name of the route to get the URI for
     * @param array[string] $params Any parameters for the URI
     * @return string The matching URI
     * @throws \MattFerris\Http\Routing\NamedRouteDoesntExistException The route
     *     name hasn't been defined
     * @throws \InvalidArgumentException The route's required parameters haven't
     *     been specified
     */
    public function generate($name, array $params = []);

    /**
     * Find a route that matches the HTTP request and then dispatch to request
     * to the route's defined action
     * 
     * @param \Psr\Http\Message\ServerRequestInterface $request The incoming request
     * @return \Psr\Http\Message\ResponseInterface|null The response
     *     returned by the last-called action, or null if no response returned or
     *     route was matched
     */
    public function dispatch(ServerRequestInterface $request);
}

