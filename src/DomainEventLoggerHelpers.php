<?php

/**
 * DomainEventLoginHelpers.php
 * Copyright (c) 2014
 * @author Matt Ferris <matt@bueller.ca>
 */

namespace MattFerris\HttpRouting;

use MattFerris\Events\AbstractLoggerHelpers;

class DomainEventLoggerHelpers extends AbstractLoggerHelpers
{
    /**
     * @param DispatchedRequestEvent $event
     * @return string
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
                $action = implode('::', $route);
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

        return 'api/rest: dispatched request "'.$route['method'].' '.$event->getRequest()->getUri()
            .'" to "'.$action.'" with arguments ('.implode(', ', $args).')';
    }

    /**
     * @param ExceptionRaisedEvent $event
     * @return string
     */
    static public function onExceptionRaisedEvent(ExceptionRaisedEvent $event)
    {
        $e = $event->getException();
        return 'api/rest: caught exception "'.get_class($e).'"'
            .' with message "'.$e->getMessage().'" in '.$e->getFile().':'.$e->getLine();
    }

    /**
     * @param ReceivedRequestEvent $event
     * @return string
     */
    static public function onReceivedRequestEvent(ReceivedRequestEvent $event)
    {
        $req = $event->getRequest();
        return 'api/rest: received request "'.$req->getMethod().' '.$req->getUri().'"';
    }

    /**
     * @param RouteNotFoundEvent $event
     * @return string
     */
    static public function onRouteNotFoundEvent(RouteNotFoundEvent $event)
    {
        return 'api/rest: no route found to dispatch request "'.$event->getRequest()->getUri().'"';
    }
}

