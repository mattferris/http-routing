<?php

/**
 * Http Routing - An HTTP routing dispatcher
 * www.bueller.ca/http-routing
 *
 * Dispatcher.php
 * @copyright Copyright (c) 2015
 * @author Matt Ferris <matt@bueller.ca>
 *
 * Licensed under BS 2-clause license
 * www.bueller.ca/http-routing/license
 */

namespace MattFerris\Http\Routing; 

use MattFerris\Provider\ConsumerInterface;
use MattFerris\Provider\ProviderInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class Dispatcher implements DispatcherInterface, ConsumerInterface
{
    /**
     * @var \MattFerris\Http\Routing\RouteInterface[] Routes added to the dispatcher
     */
    protected $routes = [];

    /**
     * @var \MattFerris\Http\Routing\RouteInterface[string] Named routes
     */
    protected $namedRoutes = [];

    /**
     * @var string The class name of the default route type
     */
    protected $defaultRouteType = '\\MattFerris\\Http\\Routing\\PathRoute';

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
     * Add a named route
     *
     * @param \MattFerris\Http\Routing\RouteInterface $route The route
     * @param string $name The route name
     * @throws \InvalidArgumentException If $name isn't a string or is empty
     * @throws \MattFerris\Http\Routing\DuplicateNamedRouteException The route
     *     name already exists
     */
    protected function addNamedRoute(RouteInterface $route, $name)
    {
        if (!is_string($name) || empty($name)) {
            throw new \InvalidArgumentException('$name expects non-empty string');
        }

        if (isset($this->namedRoutes[$name])) {
            throw new DuplicateNamedRouteException('named route "'.$name.'" already exists');
        }

        $this->namedRoutes[$name] = $route;
    }

    /**
     * Add a route object to the dispatcher
     *
     * @param \MattFerris\Http\Routing\RouteInterface $route The route to add
     * @param string $name The name of the route to get the URI for
     * @return self
     */
    public function add(RouteInterface $route, $name = null)
    {
        $this->routes[] = $route;

        if (!is_null($name)) {
            $this->addNamedRoute($route, $name);
        }

        return $this;
    }

    /**
     * Insert a route object at a specific array index
     *
     * @param \MattFerris\Http\Routing\RouteInterface $route The route to add
     * @param int $position The array index to insert the route in
     * @param string $name The name of the route to get the URI for
     * @return self
     * @throws \InvalidArgumentException If $position doesn't exist
     */
    public function insert(RouteInterface $route, $position, $name = null)
    {
        if (!is_int($position) || $position < 0 || $position > count($position)) {
            throw new \InvalidArgumentException('$position out of range');
        }

        array_splice($this->routes, $position, 0, array($route));

        if (!is_null($name)) {
            $this->addNamedRoute($route, $name);
        }

        return $this;
    }

    /**
     * Add a route by supplying all the parameters
     *
     * @param string $uri The URI to match
     * @param string|callable $action The action to dispatch the request to
     * @param string $method The HTTP method to match
     * @param string[string] $headers Any HTTP headers to match
     * @param string[string] $params Default values for parameters
     * @param string $name The name of the route to get the URI for
     * @return self
     */
    public function route($uri, $action, $method, array $headers, array $params = [], $name = null)
    {
        $class = $this->defaultRouteType;
        $this->add(new $class($uri, $action, $method, $headers, $params), $name);
        return $this;
    }

    /**
     * Add a route to match any HTTP method
     *
     * @param string $uri The URI to match
     * @param string|callable $action The action to dispatch the request to
     * @param string[string] $headers Any HTTP headers to match
     * @param string[string] $params Default values for parameters
     * @param string $name The name of the route to get the URI for
     * @return self
     */
    public function any($uri, $action, array $headers = [], array $params = [], $name = null)
    {
        return $this->route($uri, $action, null, $headers, $params, $name);
    }

    /**
     * Add a route to match an HTTP GET request
     *
     * @param string $uri The URI to match
     * @param string|callable $action The action to dispatch the request to
     * @param string[string] $headers Any HTTP headers to match
     * @param string[string] $params Default values for parameters
     * @param string $name The name of the route to get the URI for
     * @return self
     */
    public function get($uri, $action, array $headers = [], array $params = [], $name = null)
    {
        return $this->route($uri, $action, 'GET', $headers, $params, $name);
    }

    /**
     * Add a route to match an HTTP POST request
     *
     * @param string $uri The URI to match
     * @param string|callable $action The action to dispatch the request to
     * @param string[string] $headers Any HTTP headers to match
     * @param string[string] $params Default values for parameters
     * @param string $name The name of the route to get the URI for
     * @return self
     */
    public function post($uri, $action, array $headers = [], array $params = [], $name = null)
    {
        return $this->route($uri, $action, 'POST', $headers, $params, $name);
    }

    /**
     * Add a route to match an HTTP PUT request
     *
     * @param string $uri The URI to match
     * @param string|callable $action The action to dispatch the request to
     * @param string[string] $headers Any HTTP headers to match
     * @param string[string] $params Default values for parameters
     * @param string $name The name of the route to get the URI for
     * @return self
     */
    public function put($uri, $action, array $headers = [], array $params = [], $name = null)
    {
        return $this->route($uri, $action, 'PUT', $headers, $params, $name);
    }

    /**
     * Add a route to match an HTTP DELETE request
     *
     * @param string $uri The URI to match
     * @param string|callable $action The action to dispatch the request to
     * @param string[string] $headers Any HTTP headers to match
     * @param string[string] $params Default values for parameters
     * @param string $name The name of the route to get the URI for
     * @return self
     */
    public function delete($uri, $action, array $headers = [], array $params = [], $name = null)
    {
        return $this->route($uri, $action, 'DELETE', $headers, $params, $name);
    }

    /**
     * Add a route to match an HTTP HEAD request
     *
     * @param string $uri The URI to match
     * @param string|callable $action The action to dispatch the request to
     * @param string[string] $headers Any HTTP headers to match
     * @param string[string] $params Default values for parameters
     * @param string $name The name of the route to get the URI for
     * @return self
     */
    public function head($uri, $action, array $headers = [], array $params = [], $name = null)
    {
        return $this->route($uri, $action, 'HEAD', $headers, $params, $name);
    }

    /**
     * Add a route to match an HTTP OPTIONS request
     *
     * @param string $uri The URI to match
     * @param string|callable $action The action to dispatch the request to
     * @param string[string] $headers Any HTTP headers to match
     * @param string[string] $params Default values for parameters
     * @param string $name The name of the route to get the URI for
     * @return self
     */
    public function options($uri, $action, array $headers = [], array $params = [], $name = null)
    {
        return $this->route($uri, $action, 'OPTIONS', $headers, $params, $name);
    }

    /**
     * Add a route to match an HTTP TRACE request
     *
     * @param string $uri The URI to match
     * @param string|callable $action The action to dispatch the request to
     * @param string[string] $headers Any HTTP headers to match
     * @param string[string] $params Default values for parameters
     * @param string $name The name of the route to get the URI for
     * @return self
     */
    public function trace($uri, $action, array $headers = [], array $params = [], $name = null)
    {
        return $this->route($uri, $action, 'TRACE', $headers, $params, $name);
    }

    /**
     * Register a routing bundle, callind provdes() on the bundle to return
     * all the routes in the bundle. Add the routes via addRoutes().
     *
     * @param \MattFerris\Provider\ProviderInterface $bundle The bundle to register
     * @return self
     */
    public function register(ProviderInterface $bundle)
    {
        $bundle->provides($this);  
        return $this;
    }

    /**
     * Get a matching URI for a named route
     *
     * @param string $name The name of the route to get the URI for
     * @param array[string] $params Any parameters for the URI
     * @return string The matching URI
     * @throws \MattFerris\Http\Routing\NamedRouteDoesntExistException The route
     *     name hasn't been defined
     * @throws \InvalidArgumentException The route's required parameters haven't
     *     been specified, or $name isn't a string or is empty
     */
    public function generate($name, array $params = [])
    {
        if (!is_string($name) || empty($name)) {
            throw new \InvalidArgumentException('$name expects non-empty string');
        }

        if (!isset($this->namedRoutes[$name])) {
            throw new NamedRouteDoesntExistException('named route "'.$name.'" doesn\'t exist');
        }

        return $this->namedRoutes[$name]->generateUri($params);
    }

    /**
     * Find a route that matches the HTTP request and then dispatch to request
     * to the route's defined action
     * 
     * @param \Psr\Http\Message\SererRequestInterface $request The incoming request
     * @return \Psr\Http\Message\ResponseInterface|null The response
     *     returned by the last-called action, or null if no response returned or
     *     route was matched
     */
    public function dispatch(ServerRequestInterface $request)
    {
        $response = null;

        DomainEvents::dispatch(new ReceivedRequestEvent($request));

        $nroutes = count($this->routes);
        $method = $request->getMethod();
        $path = $request->getUri()->getPath();
        for ($i = 0; $i<$nroutes; $i++) {
            $route = $this->routes[$i];

            // intialize the list of injectable arguments for the action
            $args = $tmpargs = array();

            // if a specified method doesn't match, skip to the next route
            if ($route->hasMethod() && !$route->matchMethod($method, $args)) {
                continue;
            }

            // if any specified headers don't match, skip to the next route
            if ($route->hasHeaders()) {
                foreach ($route->getHeaderNames() as $header) {
                    if (!$request->hasHeader($header) || !$route->matchHeader($header, $request->getHeaderLine($header), $tmpargs)) {
                        continue 2;
                    }
                    $args = array_merge($tmpargs);
                }
            }

            // if the URI doesn't match, skip to the next route
            if (!$route->matchUri($path, $tmpargs)) {
                continue;
            }
            $args = array_merge($args, $tmpargs);

            // add the request object as an injectable argument
            $args['request'] = $request;

            $action = $route->getAction();
            if (is_callable($action)) {

                if ($action instanceof \Closure) {

                    // call closure
                    $response = $this->di->injectFunction($action, $args);

                } elseif (is_array($action)) {

                    // call object->action
                    $response = $this->di->injectMethod($action[0], $action[1], $args);

                } else {

                    // call static class::action
                    list($actionClass, $actionMethod) = explode('::', $action);
                    $response = $this->di->injectStaticMethod($actionClass, $actionMethod, $args);

                }

            } elseif (is_string($action) && strpos($action, ':') !== false) {

                list($actionClass, $actionMethod) = explode(':', $action);

                if (!method_exists($actionClass, $actionMethod)) {
                    throw new \InvalidArgumentException('$action doesn\'t exist');
                }

                // check if we've already instantiated the object,
                // if so, then use the existing object
                if (!isset($this->controllers[$actionClass])) {
                    $this->controllers[$actionClass] = $this->di->injectConstructor($actionClass, array('di' => '%DI'));
                }

                // call object->action
                $response = $this->di->injectMethod($this->controllers[$actionClass], $actionMethod, $args);

            } else {
                throw new \InvalidArgumentException('$action expects callable or "class:method"');
            }

            DomainEvents::dispatch(new DispatchedRequestEvent($request, $route, $args));

            // if we get a request returned, dispatch it
            if ($response instanceof ServerRequestInterface) {

                if ($response->getUri() != $request->getUri()
                    && $response->getMethod() != $request->getMethod()
                    && $response->getHeaders() != $request->getHeaders()) {
                    $path = $response->getUri()->getPath();
                    $method = $response->getMethod();
                    $i = 0;
                }

                $request = $response;
                $response = null;

            } elseif ($response instanceof ResponseInterface) {
                break;
            }
        }

        return $response;
    }
}

