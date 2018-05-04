<?php

use MattFerris\Http\Routing\DomainEventLoggerHelpers;
use MattFerris\Http\Routing\DispatchedRequestEvent;
use MattFerris\Http\Routing\ReceivedRequestEvent;
use Psr\Http\Message\ServerRequestInterface;

class DomainEventLoggerHelpersTest extends PHPUnit_Framework_TestCase
{
    public function getUri()
    {
        return $this->getMockBuilder('Psr\Http\Message\UriInterface')
            ->getMock();
    }

    public function getRequest()
    {
        return $this->getMockBuilder('Psr\Http\Message\ServerRequestInterface')
            ->getMock();
    }

    public function testOnDispatchRequestEvent()
    {
        $route = $this->getMockBuilder('MattFerris\Http\Routing\RouteInterface')
            ->getMock();
        $route->expects($this->once())->method('getAction')->willReturn('Controller:action');

        $uri = $this->getUri();
        $uri->expects($this->once())->method('__toString')->willReturn('/foo');

        $request = $this->getRequest();
        $request->expects($this->once())->method('getUri')->willReturn($uri);
        $request->expects($this->once())->method('getMethod')->willReturn('GET');

        $helpers = new DomainEventLoggerHelpers();

        $args = ['foo' => 'bar', 'baz' => new stdClass, 'bif' => []];
        $msg = $helpers->onDispatchedRequestEvent(new DispatchedRequestEvent($request, $route, $args));
        $this->assertEquals($msg, 'dispatched request "GET /foo" to "Controller:action" with arguments (foo="bar", baz=[stdClass], bif=[Array])');
    }

    public function testOnReceivedRequestEvent()
    {
        $uri = $this->getUri();
        $uri->expects($this->once())->method('__toString')->willReturn('/foo');

        $request = $this->getRequest();
        $request->expects($this->once())->method('getUri')->willReturn($uri);
        $request->expects($this->once())->method('getMethod')->willReturn('GET');

        $helpers = new DomainEventLoggerHelpers();

        $msg = $helpers->onReceivedRequestEvent(new ReceivedRequestEvent($request));
        $this->assertEquals($msg, 'received request "GET /foo"');
    }
}

