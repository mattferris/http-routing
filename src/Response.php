<?php

/**
 * HttpRouting - An HTTP routing dispatcher
 * www.bueler.ca/http-routing
 *
 * Response.php
 * @copyright Copyright (c) 2015
 * @author Matt Ferris <matt@bueller.ca>
 *
 * Licensed under BSD 2-clause license
 * www.bueller.ca/http-routing/license
 */

namespace MattFerris\HttpRouting;

class Response implements ResponseInterface
{
    /**
     * @var int The HTTP status code of the response
     */
    protected $code = 200;

    /**
     * @var array The HTTP headers to send with the response
     */
    protected $headers = array();

    /**
     * @var string The body of the response
     */
    protected $body = '';

    /**
     * @param mixed $body The body of the response
     * @param string $code The HTTP code of the response
     * @param string $contentType The value of the Content-type header
     */
    public function __construct($body = '', $code = 200, $contentType = 'text/html')
    {
        $this->body = $body;
        $this->code = $code;
        $this->setContentType($contentType);
    }

    /**
     * Set the HTTP status code
     *
     * @param int $code The HTTP status code
     * @return self
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * Get the HTTP status code of the response
     *
     * @return int The HTTP status code
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set the value of the specified header
     *
     * @param string $header The name of the header to set
     * @param string $value The value of the header
     * @return self
     */
    public function setHeader($header, $value)
    {
        $this->headers[$header] = $value;
        return $this;
    }

    /**
     * Get the value of the specified header
     *
     * @param string $header The name of the header to get
     * @return string The value of the header
     */
    public function getHeader($header)
    {
        if (isset($this->headers[$header])) {
            return $this->headers[$header];
        }
    }

    /**
     * Set the value of the Content-Type header
     *
     * @param string $type The value to set Content-Type header to
     * @return self
     */
    public function setContentType($type)
    {
        $this->headers['Content-Type'] = $type;
        return $this;
    }

    /**
     * Get the value of the Content-Type header
     *
     * @return string The value of the Content-Type header
     */
    public function getContentType()
    {
        return $this->headers['Content-Type'];
    }

    /**
     * Set the body of the response
     *
     * @param string $data The body value
     * @return self
     */
    public function setBody($data)
    {
        $this->body = $data;
        return $this;
    }

    /**
     * Get the response body
     *
     * @return string The response body
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Send the response headers
     *
     * @return self
     */
    public function sendHeaders()
    {
        http_response_code($this->code);
        foreach ($this->headers as $header => $value) {
            header($header.': '.$value);
        }
        return $this;
    }

    /**
     * Send the response body using echo() and sending headers using
     * sendHeaders()
     *
     * @see sendHeaders()
     */
    public function send()
    {
        $this->sendHeaders();
        echo $this->body;
    }

    /**
     * Send the response body as a stream
     *
     * @param mixed $data The data to send
     */
    public function stream($data)
    {
        echo $data;
    }
}

