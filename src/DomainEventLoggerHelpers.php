<?php

/**
 * HttpRouting - An HTTP routing dispatcher
 * www.bueller.ca/http-routing
 *
 * DomainEventLoginHelpers.php
 * @copyright Copyright (c) 2014
 * @author Matt Ferris <matt@bueller.ca>
 *
 * Licensed under BSD 2-clause license
 * www.bueller.ca/http-routing/license
 */

namespace MattFerris\HttpRouting;

use MattFerris\Events\AbstractLoggerHelpers;

class DomainEventLoggerHelpers extends AbstractLoggerHelpers
{
    /**
     * Generate a log message for DispatcheRequestEvent events
     *
     * @param \MattFerris\HttpRouting\DispatchedRequestEvent $event The dispatched event
     * @return string The generated log message
     */
    static public function onDispatchedRequestEvent(DispatchedRequestEvent $event)
    {
        // make string for route
        $route = $event->getRoute();
        $action = $route['action'];

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

        return 'dispatched request "'.$route['method'].' '.$event->getRequest()->getUri()
            .'" to "'.$action.'" with arguments ('.implode(', ', $args).')';
    }

    /**
     * Generate a log message for ExceptionRaiseEvent events
     *
     * @param \MattFerris\HttpRouting\ExceptionRaisedEvent $event The dispatched event
     * @return string The generated log message
     */
    static public function onExceptionRaisedEvent(ExceptionRaisedEvent $event)
    {
        $e = $event->getException();
        return 'caught exception "'.get_class($e).'"'
            .' with message "'.$e->getMessage().'" in '.$e->getFile().':'.$e->getLine();
    }

    /**
     * Generate a log message for ReceivedRequestEvent events
     *
     * @param \MattFerris\HttpRouting\ReceivedRequestEvent $event The dispatched event
     * @return string The generated log message
     */
    static public function onReceivedRequestEvent(ReceivedRequestEvent $event)
    {
        $req = $event->getRequest();
        return 'received request "'.$req->getMethod().' '.$req->getUri().'"';
    }

    /**
     * Generate a log message for RouteNotFoundEvent events
     *
     * @param \MattFerris\HttpRouting\RouteNotFoundEvent $event The dispatched event
     * @return string The generated log message
     */
    static public function onRouteNotFoundEvent(RouteNotFoundEvent $event)
    {
        return 'no route found to dispatch request "'.$event->getRequest()->getUri().'"';
    }
}

