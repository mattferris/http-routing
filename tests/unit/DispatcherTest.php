<?php

use MattFerris\HttpRouting\DomainEvent;
use MattFerris\HttpRouting\DomainEvents;
use MattFerris\HttpRouting\Dispatcher;
use MattFerris\HttpRouting\RequestInterface;
use MattFerris\HttpRouting\Request;
use MattFerris\HttpRouting\Response;

class DispatcherTest extends PHPUnit_Framework_TestCase
{
    public function testDispatch()
    {
        $request = new Request(array(
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/foo'
        ));

        $dispatcher = new Dispatcher();

        $dispatcher->get('^/foo$', function () {
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

        $dispatcher = new Dispatcher();

        $dispatcher->get(
            '^/foo$',
            function () { return new Response(); },
            array('Host' => '^example.com$')
        );

        $response = $dispatcher->dispatch($request);
        $this->assertInstanceOf('MattFerris\HttpRouting\ResponseInterface', $response);
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

        $dispatcher = new Dispatcher();

        $dispatcher->get(
            '^/(?P<fromUri>foo)$',
            $action,
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

        $dispatcher = new Dispatcher();
        $dispatcher
            ->get('^/foo$', function () { /* do nothing */ })
            ->get('^/foo$', function () { return new Response(); });

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

        $dispatcher = new Dispatcher();

        $foo = false;
        $dispatcher
            ->get('^/foo$', function () use ($requestB) { return $requestB; })
            ->get('^/bar$', function () use (&$foo) { $foo = true; });

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
                array('method' => 'GET', 'uri' => 'bar', 'action' => array('DispatcherTest_Stub', 'stubAction'))
            )));


        $requestA = new Request(array(
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => 'foo'
        ));

        $requestB = new Request(array(
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => 'bar'
        ));

        $dispatcher = new Dispatcher();
        $dispatcher->register($bundle);

        $response = $dispatcher->dispatch($requestA);
        $this->assertInstanceOf('MattFerris\HttpRouting\ResponseInterface', $response);

        $response = $dispatcher->dispatch($requestB);
        $this->assertInstanceOf('MattFerris\HttpRouting\ResponseInterface', $response);
    }

    /**
     * @depends testBundleRegistration
     * @expectedException MattFerris\HttpRouting\InvalidRouteCriteriaException
     * @expectedExceptionMessage missing URI
     */
    public function testBundleRegistrationMissingUri()
    {
        $bundle = $this->getMockBuilder('MattFerris\HttpRouting\BundleInterface')
            ->setMethods(array('provides'))
            ->getMock();

        $bundle->expects($this->once())
            ->method('provides')
            ->will($this->returnValue(array(
                array('method' => 'GET', 'action' => function() {})
            )));

        $dispatcher = (new Dispatcher())->register($bundle);
    }

    /**
     * @depends testBundleRegistration
     * @expectedException MattFerris\HttpRouting\InvalidRouteCriteriaException
     * @expectedExceptionMessage missing action
     */
    public function testBundleRegistrationMissingAction()
    {
        $bundle = $this->getMockBuilder('MattFerris\HttpRouting\BundleInterface')
            ->setMethods(array('provides'))
            ->getMock();

        $bundle->expects($this->once())
            ->method('provides')
            ->will($this->returnValue(array(
                array('method' => 'GET', 'uri' => 'foo')
            )));

        $dispatcher = (new Dispatcher())->register($bundle);
    }

    /**
     * @depends testBundleRegistration
     * @expectedException MattFerris\HttpRouting\InvalidRouteCriteriaException
     * @expectedExceptionMessage headers must be an array
     */
    public function testBundleRegistrationBadHeaders()
    {
        $bundle = $this->getMockBuilder('MattFerris\HttpRouting\BundleInterface')
            ->setMethods(array('provides'))
            ->getMock();

        $bundle->expects($this->once())
            ->method('provides')
            ->will($this->returnValue(array(
                array('method' => 'GET', 'uri' => 'foo', 'action' => function(){}, 'headers' => 'foo')
            )));

        $dispatcher = (new Dispatcher())->register($bundle);
    }
}

class DispatcherTest_Stub
{
    static public function stubAction()
    {
        return new Response();
    }
}
