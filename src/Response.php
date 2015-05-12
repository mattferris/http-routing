<?php

/**
 * Response.php
 * Copyright (c) 2015
 * @author Matt Ferris <matt@bueller.ca>
 */

namespace MattFerris\HttpRouting;

class Response implements ResponseInterface
{
    /**
     * @var int
     */
    protected $code = 200;

    /**
     * @var array
     */
    protected $headers = array();

    /**
     * @var array
     */
    protected $body = '';

    /**
     * @param mixed $body
     * @param string $code
     * @param string $contentType
     */
    public function __construct($body = '', $code = 200, $contentType = 'text/html')
    {
        $this->body = $body;
        $this->code = $code;
        $this->setContentType($contentType);
    }

    /**
     * @param int $code
     * @return ResponseInterface
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $header
     * @param string $value
     * @return ResponseInterface
     */
    public function setHeader($header, $value)
    {
        $this->headers[$header] = $value;
        return $this;
    }

    /**
     * @param string $header
     * @retur string
     */
    public function getHeader($header)
    {
        if (isset($this->headers[$header])) {
            return $this->headers[$header];
        }
    }

    /**
     * @param string $type
     * @return ResponseInterface
     */
    public function setContentType($type)
    {
        $this->headers['Content-Type'] = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getContentType()
    {
        return $this->headers['Content-Type'];
    }

    /**
     * @param string $data
     * @return ResponseInterface
     */
    public function setBody($data)
    {
        $this->body = $data;
        return $this;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    public function sendHeaders()
    {
        http_response_code($this->code);
        foreach ($this->headers as $header => $value) {
            header($header.': '.$value);
        }
        return $this;
    }

    public function send()
    {
        $this->sendHeaders();
        echo $this->body;
    }

    /**
     * @param mixed $data
     */
    public function stream($data)
    {
        echo $data;
    }
}

