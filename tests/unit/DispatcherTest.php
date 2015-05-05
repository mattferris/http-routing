<?php

use MattFerris\HttpRouting\DomainEvent;
use MattFerris\HttpRouting\DomainEvents;
use MattFerris\HttpRouting\Dispatcher;
use MattFerris\HttpRouting\RequestInterface;
use MattFerris\HttpRouting\Response;
use MattFerris\Di\Di;

class DispatcherTest extends PHPUnit_Framework_TestCase
{
    public function testDispatch()
    {
        $service = new Dispatcher(new Di());

        $service->addRoute('/^foo$/', function () {
            return new Response();
        });

        $request = $this->getMockBuilder('MattFerris\HttpRouting\Request')
            ->setMethods(array('getUri', 'getMethod'))
            ->getMock();

        $request->expects($this->once())
            ->method('getMethod')
            ->will($this->returnValue('GET'));

        $request->expects($this->once())
            ->method('getUri')
            ->will($this->returnValue('foo'));

        $response = $service->dispatch($request);
        $this->assertInstanceOf('MattFerris\HttpRouting\ResponseInterface', $response);
    }

    public function testRequestHeaderMatch()
    {
        $request = $this->getMockBuilder('MattFerris\HttpRouting\Request')
            ->setMethods(array('getUri', 'getMethod', 'getHeader'))
            ->getMock();

        $request->expects($this->once())
            ->method('getMethod')
            ->will($this->returnValue('GET'));

        $request->expects($this->once())
            ->method('getHeader')
            ->with('Host')
            ->will($this->returnValue('example.com'));

        $request->expects($this->once())
            ->method('getUri')
            ->will($this->returnValue('foo'));

        $service = new Dispatcher(new Di());

        $service->addRoute(
            '/^foo$/',
            function () { return new Response(); },
            'GET',
            array('Host' => '/^example.com$/')
        );

        $response = $service->dispatch($request);
        $this->assertInstanceOf('MattFerris\HttpRouting\ResponseInterface', $response);
    } 

    public function testActionArgumentInjection()
    {
        $request = $this->getMockBuilder('MattFerris\HttpRouting\Request')
            ->setMethods(array('getUri', 'getMethod', 'getHeader'))
            ->getMock();

        $request->expects($this->once())
            ->method('getMethod')
            ->will($this->returnValue('GET'));

        $request->expects($this->once())
            ->method('getHeader')
            ->with('Host')
            ->will($this->returnValue('example.com'));

        $request->expects($this->once())
            ->method('getUri')
            ->will($this->returnValue('foo'));

        $args = [];
        $action = function (RequestInterface $request, $fromUri, $fromHostHeader) use (&$args) {
            $args['request'] = $request;
            $args['fromUri'] = $fromUri;
            $args['fromHostHeader'] = $fromHostHeader;
            return new Response();
        };

        $service = new Dispatcher(new Di());

        $service->addRoute(
            '/^(?P<fromUri>foo)$/',
            $action,
            'GET',
            array('Host' => '/^(?<fromHostHeader>example.com)$/')
        );

        $response = $service->dispatch($request);

        $this->assertInstanceOf('MattFerris\HttpRouting\RequestInterface', $args['request']);
        $this->assertEquals($args['fromUri'], 'foo');
        $this->assertEquals($args['fromHostHeader'], 'example.com');
    }

    public function testFallThroughAction()
    {
        $request = $this->getMockBuilder('MattFerris\HttpRouting\Request')
            ->setMethods(array('getUri', 'getMethod'))
            ->getMock();

        $request->expects($this->exactly(2))
            ->method('getMethod')
            ->will($this->returnValue('GET'));

        $request->expects($this->once())
            ->method('getUri')
            ->will($this->returnValue('foo'));

        $service = new Dispatcher(new Di());

        $service->addRoutes([
            ['method' => 'GET', 'uri' => '/^foo$/', 'action' => function () { /* do nothing */ }],
            ['method' => 'GET', 'uri' => '/^foo$/', 'action' => function () { return new Response(); }]
        ]);

        $response = $service->dispatch($request);
        $this->assertInstanceOf('MattFerris\HttpRouting\ResponseInterface', $response);
    }

    public function testInternalRedirect()
    {
        $requestA = $this->getMockBuilder('MattFerris\HttpRouting\Request')
            ->setMethods(array('getUri', 'getMethod'))
            ->getMock();

        $requestA->expects($this->exactly(2))
            ->method('getMethod')
            ->will($this->returnValue('GET'));

        $requestA->expects($this->once())
            ->method('getUri')
            ->will($this->returnValue('foo'));

        $requestB = $this->getMockBuilder('MattFerris\HttpRouting\Request')
            ->setMethods(array('getUri', 'getMethod'))
            ->getMock();

        $requestB->expects($this->exactly(2))
            ->method('getMethod')
            ->will($this->returnValue('GET'));

        $requestB->expects($this->once())
            ->method('getUri')
            ->will($this->returnValue('bar'));

        $service = new Dispatcher(new Di());

        $foo = false;
        $service->addRoutes([
            ['uri' => '/^foo$/', 'action' => function () use ($requestB) { return $requestB; }],
            ['uri' => '/^bar$/', 'action' => function () use (&$foo) { $foo = true; }]
        ]);

        $service->dispatch($requestA);

        $this->assertTrue($foo);
    }

    public function testBundleRegistration()
    {
        $bundle = $this->getMockBuilder('MattFerris\HttpRouting\BundleInterface')
            ->setMethods(array('provides'))
            ->getMock();

        $bundle->expects($this->once())
            ->method('provides')
            ->will($this->returnValue(array(
                array('method' => 'GET', 'uri' => '#.*#', 'action' => function () {
                    $response = new Response();
                    return $response->setBody('foo');
                })
            )));

        $request = $this->getMockBuilder('MattFerris\HttpRouting\Request')
            ->setMethods(array('getUri', 'getMethod'))
            ->getMock();

        $request->expects($this->once())
            ->method('getMethod')
            ->will($this->returnValue('GET'));

        $request->expects($this->once())
            ->method('getUri')
            ->will(
                $this->returnValue('foo')
            );

        $dispatcher = new Dispatcher(new Di());
        $dispatcher->register($bundle);

        $response = $dispatcher->dispatch($request);
        $this->assertInstanceOf('MattFerris\HttpRouting\ResponseInterface', $response);
    }
}

