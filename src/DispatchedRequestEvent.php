<?php

/**
 * HttpRouting - An HTTP routing dispatcher
 * www.bueller.ca/http-routing
 *
 * DispatchedRequestEvent.php
 * @copyright Copyright (c) 2015
 * @author Matt Ferris <matt@bueller.ca>
 *
 * Licensed under BSD 2-clause license
 * www.bueller.ca/http-routing/license
 */

namespace MattFerris\HttpRouting;

use Psr\Http\Message\ServerRequestInterface;

class DispatchedRequestEvent extends AbstractRequestEvent
{
    /**
     * @var \MattFerris\HttpRouting\RouteInterface The route the event was
     *     dispatched for
     */
    protected $route;

    /**
     * @var array Additional arguments passed to the event
     */
    protected $args;

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request The request the
     *     event was dispatched for
     * @param \MattFerris\HttpRouting\RouteInterface $route The route that the
     *     request matched
     * @param array $args Any extra arguments passed to the event
     */
    public function __construct(ServerRequestInterface $request, RouteInterface $route, array $args)
    {
        $this->route = $route;
        $this->args = $args;
        parent::__construct($request);
    }

    /**
     * @return \MattFerris\HttpRouting\RouteInterface The route for the event
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * @return array Return the arguments passed to the event
     */
    public function getArgs()
    {
        return $this->args;
    }
}

