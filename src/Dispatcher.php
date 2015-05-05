<?php

/**
 * Dispatcher.php
 * Copyright (c) 2015
 * @author Matt Ferris <matt@bueller.ca>
 */

namespace MattFerris\HttpRouting;

use MattFerris\Di\ContainerInterface;

class Dispatcher implements DispatcherInterface
{
    /**
     * @var array
     */
    protected $routes = array();

    /**
     * @var ContainerInterface
     */
    protected $di;

    /**
     * @param ContainerInterface $di
     */
    public function __construct(ContainerInterface $di)
    {
        $this->di = $di;
    }

    /**
     * @param string $uri
     * @param mixed $action
     * @param string $httpMethod
     * @param array $httpHeaders
     */
    public function addRoute($uri, $action, $httpMethod = 'GET', $httpHeaders = array())
    {
        if (is_string($action) && strpos($action, '::') === false && strpos($action, ':') !== false) {

            // if class and method exist, we can use an instatiated
            // object to handle the requests
            list($class, $method) = explode(':', $action);

            if (method_exists($class, $method)) {

                // check if we've already instantiated the object,
                // if so, then use the existing object
                if (!isset($this->controllers[$class])) {
                    $this->controllers[$class] = $this->di->injectConstructor($class, array('di' => '%DI'));
                }

                $action = array($this->controllers[$class], $method);

            } else {
                throw new ActionDoesntExistException($action);
            }

        } elseif (!is_callable($action)) {
            throw new ActionDoesntExistException($action);
        }

        $this->routes[] = array(
            'uri' => $uri,
            'action' => $action,
            'method' => $httpMethod,
            'headers' => $httpHeaders
        );
    }

    /**
     * @param array $routes
     */
    public function addRoutes(array $routes)
    {
        foreach ($routes as $route) {
            if (!isset($route['method'])) {
                $route['method'] = 'get';
            }
            if (!isset($route['headers'])) {
                $route['headers'] = array();
            }
            $this->addRoute($route['uri'], $route['action'], $route['method'], $route['headers']);
        }
    }

    /**
     * @param BundleInterface
     */
    public function register(BundleInterface $bundle)
    {
        $this->addRoutes($bundle->provides());
    }

    /**
     * @param RequestInterface $request
     */
    public function dispatch(RequestInterface $request = null)
    {
        if ($request === null) {
            $request = new Request();
        }

        $response = null;
        $requestUri = $request->getUri();

        DomainEvents::dispatch(new ReceivedRequestEvent($request));

        foreach ($this->routes as $route) {

            $method = $route['method'];
            $uri = $route['uri'];
            $action = $route['action'];

            $args = array();

            $matchMethod = $request->getMethod() === $method;
            $matchPattern = preg_match($uri, $requestUri, $fromUri);
            if ($matchPattern) {
                $args = $fromUri;
            }

            $matchHeaders = false;
            if (count($route['headers']) > 0) {
                foreach ($route['headers'] as $routeHeader => $routeValue) {
                    if (preg_match($routeValue, $request->getHeader($routeHeader), $fromHeader)) {
                        $args = array_merge($args, $fromHeader);
                        $matchHeaders = true;
                    }
                }
            } else {
                // if route doesn't specify headers, then this needs
                // to be true so the route matches
                $matchHeaders = true;
            }

            if ($matchMethod && $matchPattern && $matchHeaders) {
                $args['request'] = $request;

                if ($action instanceof \Closure) {
                    $response = $this->di->injectFunction($action, $args);
                } else {
                    $response = $this->di->injectMethod($action[0], $action[1], $args);
                }
                DomainEvents::dispatch(new DispatchedRequestEvent($request, $route, $args));

                if ($response instanceof ResponseInterface) {
                    break;
                }
            }

        }

        return $response;
    }
}

