<?php

use MattFerris\HttpRouting\Request;

class RequestTest extends PHPUnit_Framework_TestCase
{
    public function testOverrideRequest()
    {
        $request = new Request(
            // $_SERVER
            array(
                'REQUEST_METHOD' => 'GET',
                'REQUEST_URI' => '/',
                'HTTP_HOST' => 'example.com',
                'HTTP_CONNECTION' => 'keep-alive',
                'HTTP_CACHE_CONTROL' => 'max-age=0',
                'HTTP_ACCEPT' => 'application/json,text/plain',
                'HTTP_ACCEPT_ENCODING' => 'gzip, deflate',
                'HTTP_ACCEPT_LANGUAGE' => 'en-CA,en',
                'HTTP_USER_AGENT' => 'Test-Agent',
                'SERVER_NAME' => 'server.example.com',
                'SERVER_ADDR' => '192.168.0.1',
                'SERVER_PORT' => 80,
                'REMOTE_ADDR' => '192.168.0.2',
                'REMOTE_PORT' => 52706,
                'PHP_AUTH_USER' => 'foo',
                'PHP_AUTH_PASS' => 'bar',
                'QUERY_STRING' => 'foo=bar&blah=bleh'
            ),
            // $_GET
            array(
                'foo' => 'bar'
            ),
            // $_POST
            array(
                'blah' => 'bleh'
            ),
            // $_COOKIE
            array(
                'bling' => 'blang'
            )
        );

        $this->assertFalse($request->isHttps());
        $this->assertEquals($request->getMethod(), 'GET');
        $this->assertEquals($request->getUri(), '/');
        $this->assertEquals($request->getHost(), 'example.com');
        $this->assertEquals($request->getConnection(), 'keep-alive');
        $this->assertEquals($request->getCacheControl(), array('max-age' => 0));
        $this->assertEquals($request->getAccept(), array('application/json', 'text/plain'));
        $this->assertEquals($request->getAcceptEncoding(), array('gzip', 'deflate'));
        $this->assertEquals($request->getAcceptLanguage(), array('en-CA', 'en'));
        $this->assertEquals($request->getUserAgent(), 'Test-Agent');
        $this->assertEquals($request->getServerName(), 'server.example.com');
        $this->assertEquals($request->getServerAddr(), '192.168.0.1');
        $this->assertEquals($request->getServerPort(), 80);
        $this->assertEquals($request->getRemoteAddr(), '192.168.0.2');
        $this->assertEquals($request->getRemotePort(), 52706);
        $this->assertEquals($request->getAuthUser(), 'foo');
        $this->assertEquals($request->getAuthPass(), 'bar');
        $this->assertEquals($request->getQueryString(), 'foo=bar&blah=bleh');
        $this->assertEquals($request->get('foo'), 'bar');
        $this->assertEquals($request->post('blah'), 'bleh');
        $this->assertEquals($request->cookie('bling'), 'blang');
    }
}

