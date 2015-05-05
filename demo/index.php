<?php

/*
 * chickadee demo site
 */

namespace mattferris\chickadee;

use mattferris\Diggity\DependencyInjector;
use mattferris\chickadee\Demo\DemoBundle;
use mattferris\events\DomainEventDispatcher;
use mattferris\events\DomainEventLogger;

require('../vendor/autoload.php');

$eventDispatcher = new DomainEventDispatcher();
DomainEvents::setDispatcher($eventDispatcher);

$eventLogger = new DomainEventLogger($eventDispatcher, function ($msg) {
    error_log($msg);
});
$eventLogger->setPrefix('chickadee: ');
DomainEventLoggerHelpers::addHelpers($eventLogger);

$dispatcher = new Dispatcher(new DependencyInjector());
$dispatcher->register(new DemoBundle());
$dispatcher->dispatch(new Request())->send();

