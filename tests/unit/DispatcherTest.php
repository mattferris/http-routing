<?php

use MattFerris\Http\Routing\DomainEvent;
use MattFerris\Http\Routing\DomainEvents;
use MattFerris\Http\Routing\Dispatcher;
use MattFerris\Http\Routing\SimpleRoute;
use Psr\Http\Message\UriInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class DispatcherTest extends PHPUnit_Framework_TestCase
{
    public function dummyAction()
    {
        return $this->getResponse();
    }

    public function nullResponseAction()
    {
    }

    public function getUri()
    {
        return $this->getMockBuilder('Psr\Http\Message\UriInterface')
            ->setMethods([
                'getPath', 'withPath', 'getUserInfo', 'withUserInfo',
                'getAuthority', 'getHost', 'withHost', 'getPort', 'withPort',
                'getQuery', 'withQuery', 'getScheme', 'withScheme',
                'getFragment', 'withFragment', '__toString'
            ])
            ->getMock();
    }

    public function getRequest()
    {
        return $this->getMockBuilder('Psr\Http\Message\ServerRequestInterface')
            ->setMethods([
                // ServerRequestInterface methods
                'getServerParams', 'getCookieParams', 'withCookieParams',
                'getQueryParams', 'withQueryParams', 'getUploadedFiles',
                'withUploadedFiles', 'getParsedBody', 'withParsedBody',
                'getAttributes', 'getAttribute', 'withAttribute',
                'withoutAttribute',

                // RequestInterface methods
                'getRequestTarget', 'withRequestTarget', 'getMethod',
                'withMethod', 'getUri', 'withUri',

                // MessageInterface methods
                'getProtocolVersion', 'withProtocolVersion', 'getHeaders',
                'hasHeader', 'getHeader', 'getHeaderLine', 'withHeader',
                'withAddedHeader', 'withoutHeader', 'getBody', 'withBody'
            ])
            ->getMock();
    }

    public function getResponse()
    {
        return $this->getMockBuilder('Psr\Http\Message\ResponseInterface')
            ->setMethods([])
            ->getMock();
    }

    public function testDispatchWithCallableAction()
    {
        $uri = $this->getUri();
        $uri->expects($this->once())
            ->method('getPath')
            ->willReturn('/foo');

        $request = $this->getRequest();
        $request->expects($this->once())
            ->method('getUri')
            ->willReturn($uri);
        
        $dispatcher = new Dispatcher();

        $test = $this;
        $this->assertEquals($dispatcher->add(new SimpleRoute('/foo', function () use ($test) {
            return $test->getResponse();
        })), $dispatcher);

        $response = $dispatcher->dispatch($request);
        $this->assertInstanceOf('Psr\Http\Message\ResponseInterface', $response);
    }

    /**
     * @testDispatchWithCallableAction
     */
    public function testDispatchWithStringAction()
    {
        $uri = $this->getUri();
        $uri->expects($this->once())
            ->method('getPath')
            ->willReturn('/foo');

        $request = $this->getRequest();
        $request->expects($this->once())
            ->method('getUri')
            ->willReturn($uri);

        $dispatcher = new Dispatcher();
        $dispatcher->add(new SimpleRoute('/foo', 'DispatcherTest:dummyAction'));
        //$dispatcher->add(new SimpleRoute('/foo', [$this, 'dummyAction']));

        $response = $dispatcher->dispatch($request);
        $this->assertInstanceOf('Psr\Http\Message\ResponseInterface', $response);
    }

    /**
     * @testDispatchWithCallableAction
     */
    public function testAddRouteViaInsert()
    {
        $uri = $this->getUri();

        $uri->expects($this->once())
            ->method('getPath')
            ->willReturn('/foo');

        $request = $this->getRequest();
        $request->expects($this->once())
            ->method('getUri')
            ->willReturn($uri);

        $test = $this;
        $route = new SimpleRoute('/foo', function () use ($test) {
            return $test->getResponse();
        });

        $dispatcher = new Dispatcher();

        // add a route for /foo, then insert a new route for /foo in it's place
        $test = $this;
        $dispatcher->any('/foo', function () use ($test) { return $test->getResponse(); });
        $this->assertEquals($dispatcher->insert($route, 0), $dispatcher);
        $response = $dispatcher->dispatch($request);

        $this->setExpectedException('\InvalidArgumentException', '$position out of range');
        $dispatcher->insert($route, 10);
    }

    /**
     * @testDispatchWithCallableAction
     */
    public function testAddRouteViaRoute()
    {
        $uri = $this->getUri();

        $uri->expects($this->once())
            ->method('getPath')
            ->willReturn('/foo');

        $request = $this->getRequest();
        $request->expects($this->once())
            ->method('getUri')
            ->willReturn($uri);

        $request->expects($this->once())
            ->method('getMethod')
            ->willReturn('GET');

        $dispatcher = new Dispatcher();

        $test = $this;
        $this->assertEquals($dispatcher->route('/foo', function() use ($test) {
            return $test->getResponse();
        }, 'GET', []), $dispatcher); 
        $response = $dispatcher->dispatch($request);

        $this->assertInstanceOf('Psr\Http\Message\ResponseInterface', $response);
    }

    /**
     * @testDispatchWithCallableAction
     */
    public function testAddRouteViaAny()
    {
        $uri = $this->getUri();

        $uri->expects($this->once())
            ->method('getPath')
            ->willReturn('/foo');

        $request = $this->getRequest();
        $request->expects($this->once())
            ->method('getUri')
            ->willReturn($uri);

        $dispatcher = new Dispatcher();

        $test = $this;
        $this->assertEquals($dispatcher->any('/foo', function () use ($test) {
            return $test->getResponse();
        }), $dispatcher);
        $response = $dispatcher->dispatch($request);

        $this->assertInstanceOf('Psr\Http\Message\ResponseInterface', $response);
    }

    /**
     * @testDispatchWithCallableAction
     */
    public function testAddRouteViaGet()
    {
        $uri = $this->getUri();

        $uri->expects($this->once())
            ->method('getPath')
            ->willReturn('/foo');

        $request = $this->getRequest();
        $request->expects($this->once())
            ->method('getUri')
            ->willReturn($uri);

        $request->expects($this->once())
            ->method('getMethod')
            ->willReturn('GET');

        $dispatcher = new Dispatcher();

        $test = $this;
        $this->assertEquals($dispatcher->get('/foo', function () use ($test) {
            return $test->getResponse();
        }), $dispatcher);
        $response = $dispatcher->dispatch($request);

        $this->assertInstanceOf('Psr\Http\Message\\ResponseInterface', $response);
    }

    /**
     * @testDispatchWithCallableAction
     */
    public function testAddRouteViaPost()
    {
        $uri = $this->getUri();

        $uri->expects($this->once())
            ->method('getPath')
            ->willReturn('/foo');

        $request = $this->getRequest();
        $request->expects($this->once())
            ->method('getUri')
            ->willReturn($uri);

        $request->expects($this->once())
            ->method('getMethod')
            ->willReturn('POST');

        $dispatcher = new Dispatcher();

        $test = $this;
        $this->assertEquals($dispatcher->post('/foo', function () use ($test) {
            return $test->getResponse();
        }), $dispatcher);
        $response = $dispatcher->dispatch($request);

        $this->assertInstanceOf('Psr\Http\Message\ResponseInterface', $response);
    }

    /**
     * @testDispatchWithCallableAction
     */
    public function testAddRouteViaPut()
    {
        $uri = $this->getUri();

        $uri->expects($this->once())
            ->method('getPath')
            ->willReturn('/foo');

        $request = $this->getRequest();
        $request->expects($this->once())
            ->method('getUri')
            ->willReturn($uri);

        $request->expects($this->once())
            ->method('getMethod')
            ->willReturn('PUT');

        $dispatcher = new Dispatcher();

        $test = $this;
        $this->assertEquals($dispatcher->put('/foo', function () use ($test) {
            return $test->getResponse();
        }), $dispatcher);
        $response = $dispatcher->dispatch($request);

        $this->assertInstanceOf('Psr\Http\Message\ResponseInterface', $response);
    }

    /**
     * @testDispatchWithCallableAction
     */
    public function testAddRouteViaDelete()
    {
        $uri = $this->getUri();

        $uri->expects($this->once())
            ->method('getPath')
            ->willReturn('/foo');

        $request = $this->getRequest();
        $request->expects($this->once())
            ->method('getUri')
            ->willReturn($uri);

        $request->expects($this->once())
            ->method('getMethod')
            ->willReturn('DELETE');

        $dispatcher = new Dispatcher();

        $test = $this;
        $this->assertEquals($dispatcher->delete('/foo', function () use ($test) {
            return $test->getResponse();
        }), $dispatcher);
        $response = $dispatcher->dispatch($request);

        $this->assertInstanceOf('Psr\Http\Message\ResponseInterface', $response);
    }

    /**
     * @testDispatchWithCallableAction
     */
    public function testAddRouteViaHead()
    {
        $uri = $this->getUri();

        $uri->expects($this->once())
            ->method('getPath')
            ->willReturn('/foo');

        $request = $this->getRequest();
        $request->expects($this->once())
            ->method('getUri')
            ->willReturn($uri);

        $request->expects($this->once())
            ->method('getMethod')
            ->willReturn('HEAD');

        $dispatcher = new Dispatcher();

        $test = $this;
        $this->assertEquals($dispatcher->head('/foo', function () use ($test) {
            return $test->getResponse();
        }), $dispatcher);
        $response = $dispatcher->dispatch($request);

        $this->assertInstanceOf('Psr\Http\Message\ResponseInterface', $response);
    }

    /**
     * @testDispatchWithCallableAction
     */
    public function testAddRouteViaOptions()
    {
        $uri = $this->getUri();

        $uri->expects($this->once())
            ->method('getPath')
            ->willReturn('/foo');

        $request = $this->getRequest();
        $request->expects($this->once())
            ->method('getUri')
            ->willReturn($uri);

        $request->expects($this->once())
            ->method('getMethod')
            ->willReturn('OPTIONS');

        $dispatcher = new Dispatcher();

        $test = $this;
        $this->assertEquals($dispatcher->options('/foo', function () use ($test) {
            return $test->getResponse();
        }), $dispatcher);
        $response = $dispatcher->dispatch($request);

        $this->assertInstanceOf('Psr\Http\Message\ResponseInterface', $response);
    }

    /**
     * @testDispatchWithCallableAction
     */
    public function testAddRouteViaTrace()
    {
        $uri = $this->getUri();

        $uri->expects($this->once())
            ->method('getPath')
            ->willReturn('/foo');

        $request = $this->getRequest();
        $request->expects($this->once())
            ->method('getUri')
            ->willReturn($uri);

        $request->expects($this->once())
            ->method('getMethod')
            ->willReturn('TRACE');

        $dispatcher = new Dispatcher();

        $test = $this;
        $this->assertEquals($dispatcher->trace('/foo', function () use ($test) {
            return $test->getResponse();
        }), $dispatcher);
        $response = $dispatcher->dispatch($request);

        $this->assertInstanceOf('Psr\Http\Message\ResponseInterface', $response);
    }

    /**
     * @depends testDispatchWithCallableAction
     */
    public function testRequestHeaderMatch()
    {
        $uri = $this->getUri();

        $uri->expects($this->once())
            ->method('getPath')
            ->willReturn('/foo');

        $request = $this->getRequest();

        $request->expects($this->once())
            ->method('getUri')
            ->willReturn($uri);

        $request->expects($this->once())
            ->method('getMethod')
            ->willReturn('GET');

        $request->expects($this->once())
            ->method('hasHeader')
            ->with('host')
            ->willReturn(true);

        $request->expects($this->once())
            ->method('getHeaderLine')
            ->with('host')
            ->willReturn('example.com');

        $dispatcher = new Dispatcher();

        $test = $this;
        $dispatcher->get(
            '/foo',
            function () use ($test) { return $test->getResponse(); },
            array('Host' => 'example.com')
        );

        $response = $dispatcher->dispatch($request);
        $this->assertInstanceOf('Psr\Http\Message\ResponseInterface', $response);
    } 

    /**
     * @depends testRequestHeaderMatch
     */
    public function testActionArgumentInjection()
    {
        $uri = $this->getUri();

        $uri->expects($this->once())
            ->method('getPath')
            ->willReturn('/foo');

        $request = $this->getRequest();

        $request->expects($this->once())
            ->method('getUri')
            ->willReturn($uri);

        $request->expects($this->once())
            ->method('getMethod')
            ->willReturn('GET');

        $request->expects($this->once())
            ->method('hasHeader')
            ->with('host')
            ->willReturn(true);

        $request->expects($this->once())
            ->method('getHeaderLine')
            ->with('host')
            ->willReturn(implode(',', ['example.com']));

        $args = array();
        $test = $this;
        $action = function (ServerRequestInterface $request, $fromUri, $fromHostHeader) use ($test, &$args) {
            $args['request'] = $request;
            $args['fromUri'] = $fromUri;
            $args['fromHostHeader'] = $fromHostHeader;
            return $test->getResponse();
        };

        $dispatcher = new Dispatcher();

        $dispatcher->get(
            '/{fromUri}',
            $action,
            array('Host' => '^(?P<fromHostHeader>.*)$')
        );

        $response = $dispatcher->dispatch($request);
        $this->assertTrue(isset($args['request']));
        $this->assertInstanceOf('Psr\Http\Message\ServerRequestInterface', $args['request']);
        $this->assertEquals($args['fromUri'], 'foo');
        $this->assertEquals($args['fromHostHeader'], 'example.com');
    }

    /**
     * @depends testDispatchWithCallableAction
     */
    public function testFallThroughAction()
    {
        $uri = $this->getUri();

        $uri->expects($this->once())
            ->method('getPath')
            ->willReturn('/foo');

        $request = $this->getRequest();

        $request->expects($this->once())
            ->method('getUri')
            ->willReturn($uri);

        $request->expects($this->once())
            ->method('getMethod')
            ->willReturn('GET');

        $dispatcher = new Dispatcher();

        $test = $this;
        $dispatcher
            ->get('/foo', 'DispatcherTest:nullResponseAction')
            ->get('/foo', function () { /* do nothing */ })
            ->get('/foo', function () use ($test) { return $test->getResponse(); });

        $response = $dispatcher->dispatch($request);
        $this->assertInstanceOf('Psr\Http\Message\ResponseInterface', $response);
    }

    /**
     * @depends testDispatchWithCallableAction
     */
    public function testInternalRedirect()
    {
        $uriA = $this->getUri();

        $uriA->expects($this->once())
            ->method('getPath')
            ->willReturn('/foo');

        $requestA = $this->getRequest();

        $requestA->expects($this->exactly(2))
            ->method('getUri')
            ->willReturn($uriA);

        $requestA->expects($this->once())
            ->method('getMethod')
            ->willReturn('GET');

        $uriB = $this->getUri();

        $uriB->expects($this->once())
            ->method('getPath')
            ->willReturn('/bar');

        $requestB = $this->getRequest();

        $requestB->expects($this->exactly(2))
            ->method('getUri')
            ->willReturn($uriB);

        $requestB->expects($this->once())
            ->method('getMethod')
            ->willReturn('GET');

        $dispatcher = new Dispatcher();

        $foo = false;
        $dispatcher
            ->get('/foo', function () use ($requestB) { return $requestB; })
            ->get('/bar', function () use (&$foo) { $foo = true; });

        $dispatcher->dispatch($requestA);

        $this->assertTrue($foo);
    }

    /**
     * @depends testInternalRedirect
     */
    public function testNonRedirectRequestResponse()
    {
        $uri = $this->getUri();
        $uri->expects($this->once())
            ->method('getPath')
            ->willReturn('/foo');

        $requestA = $this->getRequest();

        $requestA->expects($this->exactly(2))
            ->method('getUri')
            ->willReturn($uri);

        $requestA->expects($this->exactly(2))
            ->method('getMethod')
            ->willReturn('GET');

        $requestA->expects($this->once())
            ->method('getHeaders')
            ->willReturn(['Host' => 'example.com']);

        $requestB = $this->getRequest();

        $requestB->expects($this->once())
            ->method('getUri')
            ->willReturn($uri);

        $requestB->expects($this->once())
            ->method('getMethod')
            ->willReturn('GET');

        $requestB->expects($this->once())
            ->method('getHeaders')
            ->willReturn(['Host' => 'example.com']);

        $dispatcher = new Dispatcher();

        $foo = null;
        $dispatcher
            ->get('/foo', function () use ($requestB) { return $requestB; })
            ->get('/foo', function ($request) use (&$foo) { $foo = $request; });

        $dispatcher->dispatch($requestA);

        $this->assertSame($requestB, $foo);
    }

    /**
     * @depends testAddRouteViaRoute
     */
    public function testGenerate()
    {
        $dispatcher = new Dispatcher();

        // test retrieving reverse route
        $dispatcher->get('/foo', function(){}, [], [], 'foo');
        $this->assertEquals($dispatcher->generate('foo'), '/foo');

        // test reverse route with parameters
        $dispatcher->get('/bar/{baz}', function(){}, [], [], 'bar');
        $this->assertEquals($dispatcher->generate('bar', ['baz' => 'test']), '/bar/test');
    }

    /**
     * @depends testGenerate
     */
    public function testAddDuplicateNamedRoute()
    {
        $dispatcher = new Dispatcher();

        // test adding duplicate named route
        $this->setExpectedException('MattFerris\Http\Routing\DuplicateNamedRouteException', 'named route "foo" already exists');
        $dispatcher->get('/foo', function(){}, [], [], 'foo');
        $dispatcher->get('/foo2', function(){}, [], [], 'foo');
    }

    /**
     * @depends testGenerate
     */
    public function testGenerateForNonExistentNameRoute()
    {
        $dispatcher = new Dispatcher();

        // test getting non-existent named route
        $this->setExpectedException('MattFerris\Http\Routing\NamedRouteDoesntExistException', 'named route "baz" doesn\'t exist');
        $dispatcher->generate('baz');
    }

    public function testRegister()
    {
        $dispatcher = new Dispatcher();

        $provider = $this->getMockBuilder('MattFerris\Provider\ProviderInterface')
            ->setMethods(['provides'])
            ->getMock();

        $provider->expects($this->once())
            ->method('provides')
            ->with($dispatcher);

        $this->assertEquals($dispatcher->register($provider), $dispatcher);
    }
}

