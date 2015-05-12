<?php

use MattFerris\HttpRouting\Response;

class ResponseTest extends PHPUnit_Framework_TestCase
{
    public function testConstructWithDefaults()
    {
       $response = new Response();
       $this->assertEquals($response->getBody(), '');
       $this->assertEquals($response->getCode(), 200);
       $this->assertEquals($response->getContentType(), 'text/html');
    }

    /**
     * @depends testConstructWithDefaults
     */
    public function testConstructWithArguments()
    {
       $response = new Response('Hi!', 201, 'text/plain');
       $this->assertEquals($response->getBody(), 'Hi!');
       $this->assertEquals($response->getCode(), 201);
       $this->assertEquals($response->getContentType(), 'text/plain');
    }

    public function testSetters()
    {
        $response = new Response();
        $response
            ->setCode(304)
            ->setContentType('text/plain')
            ->setHeader('Connection', 'keep-alive')
            ->setBody('Hi!');

        $this->assertEquals($response->getCode(), 304);
        $this->assertEquals($response->getContentType(), 'text/plain');
        $this->assertEquals($response->getHeader('Connection'), 'keep-alive');
        $this->assertEquals($response->getBody(), 'Hi!');
    }
}

