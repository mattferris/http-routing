<?php

/**
 * HttpRouting - An HTTP routing dispatcher
 * www.bueller.ca/http-routing
 *
 * DispatcherInterface.php
 * @copyright Copyright (c) 2015
 * @author Matt Ferris <matt@bueller.ca>
 *
 * Licensed under BSD 2-clause license
 * www.bueller.ca/http-routing/license
 */

namespace MattFerris\HttpRouting;

interface DispatcherInterface
{
    /**
     * Add a route to the dispatcher
     *
     * @param string $uri The URI to match
     * @param mixed $action The action to dispatch the request to
     * @param string $httpMethod The HTTP method to match
     * @param array $httpHeaders Any HTTP headers to match
     * @return self
     * @throws \MattFerris\HttpRouting\ActionDoesntExistException The action
     *     doesn't exist or isn't callable
     */
    public function addRoute($uri, $action, $httpMethod = 'get', $httpHeaders = array());

    /**
     * Add multiple routes to the dispatcher by callind addRoute() for each
     * route in $routes
     *
     * @see addRoute()
     * @param array $routes An array of routes to add
     */
    public function addRoutes(array $routes);

    /**
     * Register a routing bundle, callind provdes() on the bundle to return
     * all the routes in the bundle. Add the routes via addRoutes().
     *
     * @see addRoutes()
     * @param \MattFerris\HttpRouting\BundleInterface $bundle The bundle to register
     */
    public function register(BundleInterface $bundle);

    /**
     * Attempt to match the passed request to a route, calling the action of
     * the matched route. If the route's action returns a Response, stop matching
     * routes and return the response. If a Request is returned, start matching
     * all over again with the new request. If nothing is returned, continue
     * matching.
     *
     * @param \MattFerris\HttpRouting\RequestInterface $request The request to match
     * @return \MattFerris\HttpRouting\Response The response from the matched action
     * @return null The request didn't match any routes
     * @throws \MattFerris\HttpRouting\InvalidHeaderException A route defined an
     *     invalid header name to match
     */
    public function dispatch(RequestInterface $request = null);
}

