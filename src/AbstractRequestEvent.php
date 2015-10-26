<?php

/**
 * Http Routing - An HTTP routing dispatcher
 * www.bueller.ca/http-routing
 *
 * AbstractRequestEvent.php
 * @copyright Copyright (c) 2015
 * @author Matt Ferris <matt@bueller.ca>
 *
 * Licensed under BSD 2-clause license
 * www.bueller.ca/http-routing/license
 */

namespace MattFerris\Http\Routing;

use Psr\Http\Message\ServerRequestInterface;

class AbstractRequestEvent extends DomainEvent
{
    /**
     * @var \Psr\Http\Message\ServerRequestInterface The request the event was
     *     dispatched for
     */
    protected $request;

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request The request the
     *     event was dispatched for
     */
    public function __construct(ServerRequestInterface $request)
    {
        $this->request = $request;
    }

    /**
     * Return the request the event was dispatched for
     *
     * @return \Psr\Http\Message\ServerRequestInterface The request the event
     *    was dispatched for
     */
    public function getRequest()
    {
        return $this->request;
    }
}

