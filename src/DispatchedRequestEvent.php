<?php

/**
 * DispatchedRequestEvent.php
 * Copyright (c) 2015
 * @author Matt Ferris <matt@bueller.ca>
 */

namespace MattFerris\HttpRouting;

class DispatchedRequestEvent extends AbstractRequestEvent
{
    /**
     * @var mixed
     */
    protected $route;

    /**
     * @var array
     */
    protected $args;

    /**
     * @param RequestInterface $request
     * @param mixed $route
     * @param array $args
     */
    public function __construct(RequestInterface $request, $route, array $args)
    {
        $this->route = $route;
        $this->args = $args;
        parent::__construct($request);
    }

    /**
     * @return mixed
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * @return array
     */
    public function getArgs()
    {
        return $this->args;
    }
}

