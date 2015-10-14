<?php

/**
 * HttpRouting - An HTTP routing dispatcher
 * www.bueller.ca/http-routing
 *
 * ExceptionRaisedEvent.php
 * @copyright Copyright (c) 2015
 * @author Matt Ferris <matt@bueller.ca>
 *
 * Licensed under BSD 2-clause license
 * www.bueller.ca/http-routing/license
 */

namespace MattFerris\HttpRouting;

class ExceptionRaisedEvent extends DomainEvent
{
    /**
     * @var \Exception The raised exception
     */
    protected $exception;

    /**
     * @param \Exception $exception The raised exception
     */
    public function __construct(\Exception $exception)
    {
        $this->exception = $exception;
    }

    /**
     * Return the raised exception
     *
     * @return \Exception The raised exception
     */
    public function getException()
    {
        return $this->exception;
    }
}

