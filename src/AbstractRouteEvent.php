<?php

/**
 * HttpRouting - An HTTP routing dispatcher
 * www.bueller.ca/http-routing
 *
 * AbstractRouteEvent.php
 * @copyright Copyright (c) 2015
 * @author Matt Ferris <matt@bueller.ca>
 *
 * Licensed under BSD 2-clause license
 * www.bueller.ca/http-routing/license
 */

namespace MattFerris\HttpRouting;

class AbstractRouteEvent extends DomainEvent
{
    /**
     * @var array The route the event was dispatched for
     */
    protected $route;

    /**
     * @param array $route The route the event was dispatched for
     */
    public function __construct(array $route)
    {
        $this->route = $route;
    }

    /**
     * Return the route the event was dispatched for
     *
     * @return array[] The request the event was dispatched for
     */
    public function getRoute()
    {
        return $this->route;
    }
}

