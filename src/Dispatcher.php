<?php

/**
 * Dispatcher.php
 * Copyright (c) 2015
 * @author Matt Ferris <matt@bueller.ca>
 */

namespace MattFerris\HttpRouting;

use MattFerris\Di\ContainerInterface;
use MattFerris\Di\Di;

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
    public function __construct(ContainerInterface $di = null)
    {
        if ($di === null) {
            $di = new Di();
        }

        $this->di = $di;
    }

    /**
     * @param string $uri
     * @param mixed $action
     * @param string $httpMethod
     * @param array $httpHeaders
     * @return $this
     */
    public function addRoute($uri, $action, $httpMethod = 'GET', $httpHeaders = array())
    {
        if (is_string($action) && strpos($action, '::') === false && strpos($action, ':') !== false) {

            // if class and method exist, we can use an instatiated
            // object to handle the requests
            list($class, $method) = explode(':', $action);

            if (method_exists($class, $method)) {

                $action = array($class, $method);

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

        return $this;
    }

    /**
     * @param array $routes
     */
    public function addRoutes(array $routes)
    {
        foreach ($routes as $route) {
            if (!isset($route['method'])) {
                $route['method'] = 'GET';
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
            $matchPattern = $matchHeaders = false;

            // if route matched method, check uri
            if ($matchMethod) {
                $matchPattern = preg_match('!'.$uri.'!', $requestUri, $fromUri);
                if ($matchPattern) {
                    $args = $fromUri;
                }
            }

            // if route matched pattern (which assumes it also matched method)
            // then check any headers
            if ($matchPattern && count($route['headers']) > 0) {
                foreach ($route['headers'] as $routeHeader => $routeValue) {

                    $headerMethod = 'get'.$routeHeader;

                    // throw exception if method doesn't exist
                    if (!method_exists($request, $headerMethod)) {
                        throw new InvalidHeaderException($routeHeader);
                    }

                    if (preg_match('!'.$routeValue.'!', $request->$headerMethod($routeHeader), $fromHeader)) {
                        $args = array_merge($args, $fromHeader);
                        $matchHeaders = true;
                    }
                }
            } else {
                // if route doesn't specify headers, then this needs
                // to be true so the route matches
                $matchHeaders = true;
            }

            // if everything matched, call the action
            if ($matchMethod && $matchPattern && $matchHeaders) {
                $args['request'] = $request;

                if ($action instanceof \Closure) {
                    $response = $this->di->injectFunction($action, $args);
                } else {
                    // check if we've already instantiated the object,
                    // if so, then use the existing object
                    $class = $action[0];
                    if (!isset($this->controllers[$class])) {
                        $this->controllers[$class] = $this->di->injectConstructor($class, array('di' => '%DI'));
                    }
                    $response = $this->di->injectMethod($this->controllers[$class], $action[1], $args);
                }
                DomainEvents::dispatch(new DispatchedRequestEvent($request, $route, $args));

                // if we get a request returned, dispatch it
                if ($response instanceof RequestInterface) {
                    $response = $this->dispatch($response);
                }

                if ($response instanceof ResponseInterface) {
                    break;
                }
            }

        }

        return $response;
    }
}

