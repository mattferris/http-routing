<?php

/**
 * Request.php
 * Copyright (c) 2015
 * @author Matt Ferris <matt@bueller.ca>
 */

namespace MattFerris\HttpRouting;

class Request implements RequestInterface
{
    /**
     * @var array
     */
    protected $request = array();

    /**
     * @param array $request
     */
    public function __construct(array $request = null)
    {
        if ($request === null) {
            $request = $_SERVER;
        }

        $this->request = $request;
    }

    /**
     * @return string
     */
    public function getScheme()
    {
        if (empty($this->request['HTTPS'])) {
            return 'http';
        } else {
            return 'https';
        }
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->request['REQUEST_METHOD'];
    }

    /**
     * @param string $header
     * @return mixed
     */
    public function getHeader($header)
    {
        switch ($header) {
            case 'Accept': return $this->request['HTTP_ACCEPT'];
            case 'Accept-Charset': return $this->request['HTTP_ACCEPT_CHARSET'];
            case 'Accept-Encoding': return $this->request['HTTP_ACCEPT_ENCODING'];
            case 'Accept-Language': return $this->request['HTTP_ACCEPT_LANGUAGE'];
            case 'Connection': return $this->request['HTTP_CONNECTION'];
            case 'Host': return $this->request['HTTP_HOST'];
            case 'User-Agent': return $this->request['HTTP_USER_AGENT'];
            default: return false;
        }
    }

    /**
     * @return string
     */
    public function getUri()
    {
        return $_GET['q'];
    }

    /**
     * @return array
     */
    public function getQueryString()
    {
        return $this->request['QUERY_STRING'];
    }

    /**
     * @return array
     */
    public function getAcceptableMimeTypes()
    {
        // split and trim types, then return
        return array_map(
            function ($n) { return trim($n); },
            explode(';', $this->getHeader('Accept'))
        );
    }
}

