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

class DispatchedRequestEvent extends AbstractRequestEvent
{
    /**
     * @var mixed The route the event was dispatched for
     */
    protected $route;

    /**
     * @var array Additional arguments passed to the event
     */
    protected $args;

    /**
     * @param \MattFerris\HttpRouting\RequestInterface $request The request the
     *     event was dispatched for
     * @param mixed $route The route that the request matched
     * @param array $args Any extra arguments passed to the event
     */
    public function __construct(RequestInterface $request, $route, array $args)
    {
        $this->route = $route;
        $this->args = $args;
        parent::__construct($request);
    }

    /**
     * @return mixed Return the route for the event
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

