<?php

/**
 * HttpRouting - An HTTP routing dispatcher
 * www.bueller.ca/http-routing
 *
 * AbstractRequestEvent.php
 * @copyright Copyright (c) 2015
 * @author Matt Ferris <matt@bueller.ca>
 *
 * Licensed under BSD 2-clause license
 * www.bueller.ca/http-routing/license
 */

namespace MattFerris\HttpRouting;

class AbstractRequestEvent extends DomainEvent
{
    /**
     * @var \MattFerris\HttpRouting\RequestInterface The request the event was
     *     dispatched for
     */
    protected $request;

    /**
     * @param \MattFerris\HttpRouting\RequestInterface $request The request the
     *     event was dispatched for
     */
    public function __construct(RequestInterface $request)
    {
        $this->request = $request;
    }

    /**
     * Return the request the event was dispatched for
     *
     * @return \MattFerris\HttpRouting\RequestInterface The request the event
     *    was dispatched for
     */
    public function getRequest()
    {
        return $this->request;
    }
}

