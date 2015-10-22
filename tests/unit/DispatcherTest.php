<?php

use MattFerris\HttpRouting\DomainEvent;
use MattFerris\HttpRouting\DomainEvents;
use MattFerris\HttpRouting\Dispatcher;
use MattFerris\HttpRouting\RequestInterface;
use MattFerris\HttpRouting\Request;
use MattFerris\HttpRouting\Response;
use MattFerris\HttpRouting\SimpleRoute;

class DispatcherTest extends PHPUnit_Framework_TestCase
{
    public function testDispatch()
    {
        $request = new Request(array(
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/foo'
        ));

        $dispatcher = new Dispatcher();

        $dispatcher->add(new SimpleRoute('/foo', function () {
            return new Response();
        }));

        $response = $dispatcher->dispatch($request);
        $this->assertInstanceOf('MattFerris\HttpRouting\ResponseInterface', $response);
    }

    /**
     * @testDispatch
     */
    public function testAddRouteViaInsert()
    {
        $request = new Request(array(
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/foo'
        ));

        $route = new SimpleRoute('/foo', function () {
            return new Response('via insert()');
        }, 'GET');

        // add a route for /foo, then insert a new route for /foo in it's place
        $dispatcher = new Dispatcher();
        $dispatcher->get('/foo', function () { return new Response('via get()'); });
        $dispatcher->insert($route, 0);
        $response = $dispatcher->dispatch($request);

        $this->assertEquals('via insert()', $response->getBody());

        $this->setExpectedException('\InvalidArgumentException', '$position out of range');
        $dispatcher->insert($route, 10);
    }

    /**
     * @testDispatch
     */
    public function testAddRouteViaRoute()
    {
        $request = new Request(array(
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/foo'
        ));

        $dispatcher = new Dispatcher();
        $dispatcher->route('/foo', function() {
            return new Response();
        }, 'GET', array()); 
        $response = $dispatcher->dispatch($request);

        $this->assertInstanceOf('MattFerris\HttpRouting\ResponseInterface', $response);
    }

    /**
     * @testDispatch
     */
    public function testAddRouteViaAny()
    {
        $request = new Request(array(
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/foo'
        ));

        $dispatcher = new Dispatcher();
        $dispatcher->any('/foo', function () {
            return new Response();
        });
        $response = $dispatcher->dispatch($request);

        $this->assertInstanceOf('MattFerris\HttpRouting\ResponseInterface', $response);
    }

    /**
     * @testDispatch
     */
    public function testAddRouteViaGet()
    {
        $request = new Request(array(
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/foo'
        ));

        $dispatcher = new Dispatcher();
        $dispatcher->get('/foo', function () {
            return new Response();
        });
        $response = $dispatcher->dispatch($request);

        $this->assertInstanceOf('MattFerris\HttpRouting\ResponseInterface', $response);
    }

    /**
     * @testDispatch
     */
    public function testAddRouteViaPost()
    {
        $request = new Request(array(
            'REQUEST_METHOD' => 'POST',
            'REQUEST_URI' => '/foo'
        ));

        $dispatcher = new Dispatcher();
        $dispatcher->post('/foo', function () {
            return new Response();
        });
        $response = $dispatcher->dispatch($request);

        $this->assertInstanceOf('MattFerris\HttpRouting\ResponseInterface', $response);
    }

    /**
     * @testDispatch
     */
    public function testAddRouteViaPut()
    {
        $request = new Request(array(
            'REQUEST_METHOD' => 'PUT',
            'REQUEST_URI' => '/foo'
        ));

        $dispatcher = new Dispatcher();
        $dispatcher->put('/foo', function () {
            return new Response();
        });
        $response = $dispatcher->dispatch($request);

        $this->assertInstanceOf('MattFerris\HttpRouting\ResponseInterface', $response);
    }

    /**
     * @testDispatch
     */
    public function testAddRouteViaDelete()
    {
        $request = new Request(array(
            'REQUEST_METHOD' => 'DELETE',
            'REQUEST_URI' => '/foo'
        ));

        $dispatcher = new Dispatcher();
        $dispatcher->delete('/foo', function () {
            return new Response();
        });
        $response = $dispatcher->dispatch($request);

        $this->assertInstanceOf('MattFerris\HttpRouting\ResponseInterface', $response);
    }

    /**
     * @testDispatch
     */
    public function testAddRouteViaHead()
    {
        $request = new Request(array(
            'REQUEST_METHOD' => 'HEAD',
            'REQUEST_URI' => '/foo'
        ));

        $dispatcher = new Dispatcher();
        $dispatcher->head('/foo', function () {
            return new Response();
        });
        $response = $dispatcher->dispatch($request);

        $this->assertInstanceOf('MattFerris\HttpRouting\ResponseInterface', $response);
    }

    /**
     * @testDispatch
     */
    public function testAddRouteViaOptions()
    {
        $request = new Request(array(
            'REQUEST_METHOD' => 'OPTIONS',
            'REQUEST_URI' => '/foo'
        ));

        $dispatcher = new Dispatcher();
        $dispatcher->options('/foo', function () {
            return new Response();
        });
        $response = $dispatcher->dispatch($request);

        $this->assertInstanceOf('MattFerris\HttpRouting\ResponseInterface', $response);
    }

    /**
     * @testDispatch
     */
    public function testAddRouteViaTrace()
    {
        $request = new Request(array(
            'REQUEST_METHOD' => 'TRACE',
            'REQUEST_URI' => '/foo'
        ));

        $dispatcher = new Dispatcher();
        $dispatcher->trace('/foo', function () {
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
}

class DispatcherTest_Stub
{
    static public function stubAction()
    {
        return new Response();
    }
}
