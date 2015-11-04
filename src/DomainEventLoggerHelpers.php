<?php

/**
 * Http Routing - An HTTP routing dispatcher
 * www.bueller.ca/http-routing
 *
 * DomainEventLoginHelpers.php
 * @copyright Copyright (c) 2014
 * @author Matt Ferris <matt@bueller.ca>
 *
 * Licensed under BSD 2-clause license
 * www.bueller.ca/http-routing/license
 */

namespace MattFerris\Http\Routing;

use MattFerris\Events\AbstractLoggerHelpers;
use MattFerris\Evetnts\LoggerInterface;

class DomainEventLoggerHelpers extends AbstractLoggerHelpers
{
    /**
     * Register helpers with logger
     *
     * @param \MattFerris\Events\LoggerInterface $logger The logging dispatcher
     */
    public function register(LoggerInterface $logger)
    {
        $ns = __NAMESPACE__.'\\';
        $logger
            ->addHelper($ns.'DispatchedRequestEvent', [$this, 'onDispatchedRequestEvent'])
            ->addHelper($ns.'ReceivedRequestEvent', [$this, 'onReceivedRequestEvent']);
    }

    /**
     * Generate a log message for DispatcheRequestEvent events
     *
     * @param \MattFerris\Http\Routing\DispatchedRequestEvent $event The dispatched event
     * @return string The generated log message
     */
    public function onDispatchedRequestEvent(DispatchedRequestEvent $event)
    {
        // make string for route
        $route = $event->getRoute();
        $action = $route->getAction();

        if ($action instanceof \Closure) {
            $action = 'Closure';
        } elseif (is_array($action)) {
            if (is_object($action[0])) {
                $class = get_class($action[0]);
                $method = $action[1];
                $action = $class.'->'.$method.'()';
            } else {
                $action = implode('::', $action);
            }
        }

        // make string for args
        $eventArgs = $event->getArgs();

        $args = array();
        foreach ($eventArgs as $name => $value) {
            if (is_string($name)) {
                if (is_object($value)) {
                    $args[] = $name.'=['.get_class($value).']';
                } else {
                    $args[] = $name.'="'.addslashes($value).'"';
                }
            }
        }

        $request = $event->getRequest();
        $uri = (string)$event->getRequest()->getUri();
        $method = $request->getMethod();

        return 'dispatched request "'.$method.' '.$uri.'" to "'.$action.
            '" with arguments ('.implode(', ', $args).')';
    }

    /**
     * Generate a log message for ReceivedRequestEvent events
     *
     * @param \MattFerris\Http\Routing\ReceivedRequestEvent $event The dispatched event
     * @return string The generated log message
     */
    public function onReceivedRequestEvent(ReceivedRequestEvent $event)
    {
        $req = $event->getRequest();
        return 'received request "'.$req->getMethod().' '.$req->getUri().'"';
    }
}

