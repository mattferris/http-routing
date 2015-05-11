<?php

use MattFerris\HttpRouting\DomainEvent;
use MattFerris\HttpRouting\DomainEvents;
use MattFerris\HttpRouting\Dispatcher;
use MattFerris\HttpRouting\RequestInterface;
use MattFerris\HttpRouting\Request;
use MattFerris\HttpRouting\Response;
use MattFerris\Di\Di;

class DispatcherTest extends PHPUnit_Framework_TestCase
{
    public function testDispatch()
    {
        $request = new Request(array(
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/foo'
        ));

        $dispatcher = new Dispatcher(new Di());

        $dispatcher->addRoute('^/foo$', function () {
            return new Response();
        });

        $response = $dispatcher->dispatch($request);
        $this->assertInstanceOf('MattFerris\HttpRouting\ResponseInterface', $response);
    }

    /**
     * @depends testDispatch
     */
    public function testRequestHeaderMatch()
    {
        $request = new Request(array(
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/foo',
            'HTTP_HOST' => 'example.com'
        ));

        $dispatcher = new Dispatcher(new Di());

        $dispatcher->addRoute(
            '^/foo$',
            function () { return new Response(); },
            'GET',
            array('Host' => '^example.com$')
        );

        $response = $dispatcher->dispatch($request);
        $this->assertInstanceOf('MattFerris\HttpRouting\ResponseInterface', $response);
    } 

    /**
     * @depends testRequestHeaderMatch
     * @expectedException MattFerris\HttpRouting\InvalidHeaderException
     */
    public function testInvalidHeaderInMatch()
    {
        $request = new Request(array(
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/foo'
        ));

        $dispatcher = new Dispatcher(new Di());
        $dispatcher
            ->addRoute('^/foo$', function () {}, 'GET', array('Foo' => '^bar$'))
            ->dispatch($request);
    }

    /**
     * @depends testRequestHeaderMatch
     */
    public function testActionArgumentInjection()
    {
        $request = new Request(array(
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/foo',
            'HTTP_HOST' => 'example.com'
        ));

        $args = array();
        $action = function (RequestInterface $request, $fromUri, $fromHostHeader) use (&$args) {
            $args['request'] = $request;
            $args['fromUri'] = $fromUri;
            $args['fromHostHeader'] = $fromHostHeader;
            return new Response();
        };

        $dispatcher = new Dispatcher(new Di());

        $dispatcher->addRoute(
            '^/(?P<fromUri>foo)$',
            $action,
            'GET',
            array('Host' => '^(?<fromHostHeader>example.com)$')
        );

        $response = $dispatcher->dispatch($request);

        $this->assertInstanceOf('MattFerris\HttpRouting\RequestInterface', $args['request']);
        $this->assertEquals($args['fromUri'], 'foo');
        $this->assertEquals($args['fromHostHeader'], 'example.com');
    }

    /**
     * @depends testDispatch
     */
    public function testFallThroughAction()
    {
        $request = new Request(array(
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/foo'
        ));

        $dispatcher = new Dispatcher(new Di());

        $dispatcher->addRoutes(array(
            array('method' => 'GET', 'uri' => '^/foo$', 'action' => function () { /* do nothing */ }),
            array('method' => 'GET', 'uri' => '^/foo$', 'action' => function () { return new Response(); })
        ));

        $response = $dispatcher->dispatch($request);
        $this->assertInstanceOf('MattFerris\HttpRouting\ResponseInterface', $response);
    }

    /**
     * @depends testDispatch
     */
    public function testInternalRedirect()
    {
        $requestA = new Request(array(
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/foo'
        ));

        $requestB = new Request(array(
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/bar'
        ));

        $dispatcher = new Dispatcher(new Di());

        $foo = false;
        $dispatcher->addRoutes(array(
            array('uri' => '^/foo$', 'action' => function () use ($requestB) { return $requestB; }),
            array('uri' => '^/bar$', 'action' => function () use (&$foo) { $foo = true; })
        ));

        $dispatcher->dispatch($requestA);

        $this->assertTrue($foo);
    }

    /**
     * @depends testDispatch
     */
    public function testBundleRegistration()
    {
        $bundle = $this->getMockBuilder('MattFerris\HttpRouting\BundleInterface')
            ->setMethods(array('provides'))
            ->getMock();

        $bundle->expects($this->once())
            ->method('provides')
            ->will($this->returnValue(array(
                array('method' => 'GET', 'uri' => 'foo', 'action' => function () {
                    return new Response();
                }),
                array('method' => 'GET', 'uri' => 'bar', 'action' => 'DispatcherTest_Stub:stubAction')
            )));


        $requestA = new Request(array(
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => 'foo'
        ));

        $requestB = new Request(array(
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => 'bar'
        ));

        $dispatcher = new Dispatcher(new Di());
        $dispatcher->register($bundle);

        $response = $dispatcher->dispatch($requestA);
        $this->assertInstanceOf('MattFerris\HttpRouting\ResponseInterface', $response);

        $response = $dispatcher->dispatch($requestB);
        $this->assertInstanceOf('MattFerris\HttpRouting\ResponseInterface', $response);
    }
}

class DispatcherTest_Stub
{
    static public function stubAction()
    {
        return new Response();
    }
}
