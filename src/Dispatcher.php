<?php

/**
 * HttpRouting - An HTTP routing dispatcher
 * www.bueller.ca/http-routing
 *
 * Dispatcher.php
 * @copyright Copyright (c) 2015
 * @author Matt Ferris <matt@bueller.ca>
 *
 * Licensed under BS 2-clause license
 * www.bueller.ca/http-routing/license
 */

namespace MattFerris\HttpRouting; 

class Dispatcher implements DispatcherInterface
{
    /**
     * @var \MattFerris\HttpRouting\RouteInterface[] Routes added to the dispatcher
     */
    protected $routes = array();

    /**
     * @var string The class name of the default route type
     */
    protected $defaultRouteType = '\\MattFerris\\HttpRouting\\PathRoute';

    /**
     * @var \MattFerris\Di\ContainerInterface The dependency injector instance
     */
    protected $di;

    /**
     * @param \MattFerris\Di\ContainerInterface $di A DI instance
     */
    public function __construct(\MattFerris\Di\ContainerInterface $di = null)
    {
        if ($di === null) {
            $di = new \MattFerris\Di\Di();
        }
        $this->di = $di;
    }

    /**
     * Set the default class for route objects
     *
     * @param string $class The route class
     * @return self
     * @throws \InvalidArgumentException If $class isn't a non-empty string
     */
    public function setDefaultRouteType($class)
    {
        if (!is_string($class) || empty($class)) {
            throw new \InvalidArgumentException('$class expects non-empty string');
        }
        $this->defaultRouteType = $class;
    }

    /**
     * Add a route object to the dispatcher
     *
     * @param \MattFerris\HttpRouting\RouteInterface $route The route to add
     * @return self
     */
    public function add(RouteInterface $route)
    {
        $this->routes[] = $route;
        return $this;
    }

    /**
     * Insert a route object at a specific array index
     *
     * @param \MattFerris\HttpRouting\RouteInterface $route The route to add
     * @param int $position The array index to insert the route in
     * @return self
     * @throws \InvalidArgumentException If $position doesn't exist
     */
    public function insert(RouteInterface $route, $position)
    {
        if (!is_int($position) || $position < 0 || $position > count($position)) {
            throw new \InvalidArgumentException('$position out of range');
        }
        array_splice($this->routes, $position, 0, array($route));
        return $this;
    }

    /**
     * Add a route by supplying all the parameters
     *
     * @param string $uri The URI to match
     * @param callable $action The action to dispatch the request to
     * @param string $method The HTTP method to match
     * @param string[string] $headers Any HTTP headers to match
     * @return self
     */
    public function route($uri, callable $action, $method, array $headers)
    {
        $class = $this->defaultRouteType;
        return $this->add(new $class($uri, $action, $method, $headers));
    }

    /**
     * Add a route to match any HTTP method
     *
     * @param string $uri The URI to match
     * @param callable $action The action to dispatch the request to
     * @param string[string] $headers Any HTTP headers to match
     * @return self
     */
    public function any($uri, callable $action, array $headers = array())
    {
        return $this->route($uri, $action, null, $headers);
    }

    /**
     * Add a route to match an HTTP GET request
     *
     * @param string $uri The URI to match
     * @param callable $action The action to dispatch the request to
     * @param string[string] $headers Any HTTP headers to match
     * @return self
     */
    public function get($uri, callable $action, array $headers = array())
    {
        return $this->route($uri, $action, 'GET', $headers);
    }

    /**
     * Add a route to match an HTTP POST request
     *
     * @param string $uri The URI to match
     * @param callable $action The action to dispatch the request to
     * @param string[string] $headers Any HTTP headers to match
     * @return self
     */
    public function post($uri, callable $action, array $headers = array())
    {
        return $this->route($uri, $action, 'POST', $headers);
    }

    /**
     * Add a route to match an HTTP PUT request
     *
     * @param string $uri The URI to match
     * @param callable $action The action to dispatch the request to
     * @param string[string] $headers Any HTTP headers to match
     * @return self
     */
    public function put($uri, callable $action, array $headers = array())
    {
        return $this->route($uri, $action, 'PUT', $headers);
    }

    /**
     * Add a route to match an HTTP DELETE request
     *
     * @param string $uri The URI to match
     * @param callable $action The action to dispatch the request to
     * @param string[string] $headers Any HTTP headers to match
     * @return self
     */
    public function delete($uri, callable $action, array $headers = array())
    {
        return $this->route($uri, $action, 'DELETE', $headers);
    }

    /**
     * Add a route to match an HTTP HEAD request
     *
     * @param string $uri The URI to match
     * @param callable $action The action to dispatch the request to
     * @param string[string] $headers Any HTTP headers to match
     * @return self
     */
    public function head($uri, callable $action, array $headers = array())
    {
        return $this->route($uri, $action, 'HEAD', $headers);
    }

    /**
     * Add a route to match an HTTP OPTIONS request
     *
     * @param string $uri The URI to match
     * @param callable $action The action to dispatch the request to
     * @param string[string] $headers Any HTTP headers to match
     * @return self
     */
    public function options($uri, callable $action, array $headers = array())
    {
        return $this->route($uri, $action, 'OPTIONS', $headers);
    }

    /**
     * Add a route to match an HTTP TRACE request
     *
     * @param string $uri The URI to match
     * @param callable $action The action to dispatch the request to
     * @param string[string] $headers Any HTTP headers to match
     * @return self
     */
    public function trace($uri, callable $action, array $headers = array())
    {
        return $this->route($uri, $action, 'TRACE', $headers);
    }

    /**
     * Register a routing bundle, callind provdes() on the bundle to return
     * all the routes in the bundle. Add the routes via addRoutes().
     *
     * @param \MattFerris\HttpRouting\BundleInterface $bundle The bundle to register
     * @return self
     * @throws \MattFerris\HttpRouting\InvalidRouteCriteriaException If the
     *    criteria for a specified route is invalid or missing
     */
    public function register(BundleInterface $bundle)
    {
        foreach ($bundle->provides() as $criteria) {
            if (!isset($criteria['uri'])) {
                throw new InvalidRouteCriteriaException('missing URI');
            }

            if (!isset($criteria['action'])) {
                throw new InvalidRouteCriteriaException('missing action');
            }

            if (!isset($criteria['method'])) {
                $criteria['method'] = null;
            }

            if (!isset($criteria['headers'])) {
                $criteria['headers'] = array();
            } elseif (!is_array($criteria['headers'])) {
                throw new InvalidRouteCriteriaException('headers must be an array');
            }

            $this->route($criteria['uri'], $criteria['action'], $criteria['method'], $criteria['headers']);
        }

        return $this;
    }

    /**
     * Find a route that matches the HTTP request and then dispatch to request
     * to the route's defined action
     * 
     * @param \MattFerris\HttpRouting\RequestInterface $request The incoming request
     * @return \MattFerris\HttpRouting\ResponseInterface|null The response
     *     returned by the last-called action, or null if no response returned or
     *     route was matched
     */
    public function dispatch(RequestInterface $request = null)
    {
        if ($request === null) {
            $request = new Request();
        }

        $response = null;

        DomainEvents::dispatch(new ReceivedRequestEvent($request));

        $nroutes = count($this->routes);
        for ($i = 0; $i<$nroutes; $i++) {
            $route = $this->routes[$i];

            // intialize the list of injectable arguments for the action
            $args = $tmpargs = array();

            // if a specified method doesn't match, skip to the next route
            if ($route->hasMethod() && !$route->matchMethod($request->getMethod(), $args)) {
                continue;
            }

            // if any specified headers don't match, skip to the next route
            if ($route->hasHeaders()) {
                foreach ($route->getHeaderNames() as $header) {
                    if (method_exists($request, 'get'.$header)) {
                        $method = 'get'.$header;
                        if (!$route->matchHeader($header, $request->$method(), $tmpargs)) {
                            continue 2;
                        }
                    } elseif (!$route->matchHeader($header, $request->getHeader($header), $tmpargs)) {
                        continue 2;
                    }
                    $args = array_merge($tmpargs);
                }
            }

            // if the URI doesn't match, skip to the next route
            if (!$route->matchUri($request->getUri(), $tmpargs)) {
                continue;
            }
            $args = array_merge($args, $tmpargs);

            // add the request object as an injectable argument
            $args['request'] = $request;

            $action = $route->getAction();
            if ($action instanceof \Closure) {
                $response = $this->di->injectFunction($action, $args);
            } else {
                if (is_string($action)) {
                    $action = explode('::', $action);
                }

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
                $request = $response;
                $response = null;
                $i = 0;
            } elseif ($response instanceof ResponseInterface) {
                break;
            }
        }

        return $response;
    }
}

