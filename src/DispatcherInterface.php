<?php

/**
 * HttpRouting - An HTTP routing dispatcher
 * www.bueller.ca/http-routing
 *
 * DispatcherInterface.php
 * @copyright Copyright (c) 2015
 * @author Matt Ferris <matt@bueller.ca>
 *
 * Licensed under BS 2-clause license
 * www.bueller.ca/http-routing/license
 */

namespace MattFerris\HttpRouting; 

use Psr\Http\Message\ServerRequestInterface;

interface DispatcherInterface
{
    /**
     * Add a route object to the dispatcher
     *
     * @param \MattFerris\HttpRouting\RouteInterface $route The route to add
     * @return self
     */
    public function add(RouteInterface $route);

    /**
     * Insert a route object at a specific array index
     *
     * @param \MattFerris\HttpRouting\RouteInterface $route The route to add
     * @param int $position The array index to insert the route in
     * @return self
     * @throws \InvalidArgumentException If $position doesn't exist
     */
    public function insert(RouteInterface $route, $position);

    /**
     * Add a route by supplying all the parameters
     *
     * @param string $uri The URI to match
     * @param callable $action The action to dispatch the request to
     * @param string[string] $headers Any HTTP headers to match
     * @param string $method The HTTP method to match
     * @return self
     */
    public function route($uri, callable $action, $method, array $headers);

    /**
     * Add a route to match any HTTP method
     *
     * @param string $uri The URI to match
     * @param callable $action The action to dispatch the request to
     * @param string[string] $headers Any HTTP headers to match
     * @return self
     */
    public function any($uri, callable $action, array $headers = array());

    /**
     * Add a route to match an HTTP GET request
     *
     * @param string $uri The URI to match
     * @param callable $action The action to dispatch the request to
     * @param string[string] $headers Any HTTP headers to match
     * @return self
     */
    public function get($uri, callable $action, array $headers = array());

    /**
     * Add a route to match an HTTP POST request
     *
     * @param string $uri The URI to match
     * @param callable $action The action to dispatch the request to
     * @param string[string] $headers Any HTTP headers to match
     * @return self
     */
    public function post($uri, callable $action, array $headers = array());

    /**
     * Add a route to match an HTTP PUT request
     *
     * @param string $uri The URI to match
     * @param callable $action The action to dispatch the request to
     * @param string[string] $headers Any HTTP headers to match
     * @return self
     */
    public function put($uri, callable $action, array $headers = array());

    /**
     * Add a route to match an HTTP DELETE request
     *
     * @param string $uri The URI to match
     * @param callable $action The action to dispatch the request to
     * @param string[string] $headers Any HTTP headers to match
     * @return self
     */
    public function delete($uri, callable $action, array $headers = array());

    /**
     * Add a route to match an HTTP HEAD request
     *
     * @param string $uri The URI to match
     * @param callable $action The action to dispatch the request to
     * @param string[string] $headers Any HTTP headers to match
     * @return self
     */
    public function head($uri, callable $action, array $headers = array());

    /**
     * Add a route to match an HTTP OPTIONS request
     *
     * @param string $uri The URI to match
     * @param callable $action The action to dispatch the request to
     * @param string[string] $headers Any HTTP headers to match
     * @return self
     */
    public function options($uri, callable $action, array $headers = array());

    /**
     * Add a route to match an HTTP TRACE request
     *
     * @param string $uri The URI to match
     * @param callable $action The action to dispatch the request to
     * @param string[string] $headers Any HTTP headers to match
     * @return self
     */
    public function trace($uri, callable $action, array $headers = array());

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

