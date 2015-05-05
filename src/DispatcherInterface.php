<?php

/**
 * DispatcherInterface.php
 * Copyright (c) 2015
 * @author Matt Ferris <matt@bueller.ca>
 */

namespace MattFerris\HttpRouting;

interface DispatcherInterface
{
    /**
     * @param string $uri
     * @param mixed $action
     * @param string $httpMethod
     * @param array $httpHeaders
     */
    public function addRoute($uri, $action, $httpMethod = 'get', $httpHeaders = array());

    /**
     * @param array $routes
     */
    public function addRoutes(array $routes);

    /**
     * @param BunbleInterface $bundle
     */
    public function register(BundleInterface $bundle);

    /**
     * @param RequestInterface $request
     */
    public function dispatch(RequestInterface $request = null);
}

