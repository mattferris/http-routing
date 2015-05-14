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
    protected $server = array();

    /**
     * @var array
     */
    protected $_get = array();

    /**
     * @var array
     */
    protected $_post = array();

    /**
     * @var array
     */
    protected $_cookie = array();

    /**
     * @var array
     */
    protected $headers = array();

    /**
     * @var array
     */
    protected $attributes = array();

    /**
     * @param array $server
     * @param array $get
     * @param array $post
     * @param array $cookie
     * @param array $headers
     */
    public function __construct(array $server = null, array $get = null, array $post = null, array $cookie = null, array $headers = null)
    {
        if ($server === null) {
            $server = $_SERVER;
        }

        if ($get === null) {
            $get = $_GET;
        }

        if ($post === null) {
            $post = $_POST;
        }

        if ($cookie === null) {
            $cookie = $_COOKIE;
        }

        if ($headers === null) {
            if (function_exists('getallheaders')) {
                $headers = getallheaders();
            } else {
                $headers = array();
            }
        }

        $this->server = $server;
        $this->_get = $get;
        $this->_post = $post;
        $this->_cookie = $cookie;
        $this->headers = $headers;
    }


    /**
     * @param string $list
     * @param bool $keyValue
     * @return array
     */
    protected function parseList($list, $keyValue = false)
    {
        // explode and trim
        $list = array_map(
            function ($v) { return trim($v); },
            explode(',', $list)
        );

        if ($keyValue === true) {
            $newList = array();
            foreach ($list as $pair) {
                $parts = explode('=', $pair);
                if ($parts[1] === null) {
                    $parts[1] = true;
                }
                $newList[$parts[0]] = $parts[1];
            }
            $list = $newList;
        }

        return $list;
    }

    /**
     * @return bool
     */
    public function isHttps()
    {
        return !empty($this->server['HTTPS']);
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->server['REQUEST_METHOD'];
    }

    /**
     * @return string
     */
    public function getUri()
    {
        return $this->server['REQUEST_URI'];
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->server['HTTP_HOST'];
    }

    /**
     * @return string
     */
    public function getConnection()
    {
        return $this->server['HTTP_CONNECTION'];
    }

    /**
     * @return array
     */
    public function getCacheControl()
    {
        $cacheControl = array();

        // check if cache control header was set
        if (!empty($this->server['HTTP_CACHE_CONTROL'])) {

            // check if header has been parsed
            if (!is_array($this->server['HTTP_CACHE_CONTROL'])) {

                $cacheControl = $this->parseList($this->server['HTTP_CACHE_CONTROL'], true);

                // replace unparsed value with parsed value
                $this->server['HTTP_CACHE_CONTROL'] = $cacheControl;
                
            } else {

                // use the previously parsed value
                $cacheControl = $this->server['HTTP_CACHE_CONTROL'];

            }
        }

        return $cacheControl;
    }

    /**
     * @return array
     */
    public function getAccept()
    {
        $accept = array();

        // check if accept header was set
        if (!empty($this->server['HTTP_ACCEPT'])) {

            // check if header has been parsed
            if (!is_array($this->server['HTTP_ACCEPT'])) {

                $accept = $this->parseList($this->server['HTTP_ACCEPT']);

                // replace unparsed value with parsed value
                $this->server['HTTP_ACCEPT'] = $accept;

            } else {

                // use the previously parsed value
                $accept = $this->server['HTTP_ACCEPT'];

            }
        }

        return $accept;
    }

    /**
     * @return array
     */
    public function getAcceptEncoding()
    {
        $accept = array();

        // check if accept header was set
        if (!empty($this->server['HTTP_ACCEPT_ENCODING'])) {

            // check if header has been parsed
            if (!is_array($this->server['HTTP_ACCEPT_ENCODING'])) {

                $accept = $this->parseList($this->server['HTTP_ACCEPT_ENCODING']);

                // replace unparsed value with parsed value
                $this->server['HTTP_ACCEPT_ENCODING'] = $accept;

            } else {

                // use the previously parsed value
                $accept = $this->server['HTTP_ACCEPT_ENCODING'];

            }
        }

        return $accept;
    }

    /**
     * @return array
     */
    public function getAcceptLanguage()
    {
        $accept = array();

        // check if accept header was set
        if (!empty($this->server['HTTP_ACCEPT_LANGUAGE'])) {

            // check if header has been parsed
            if (!is_array($this->server['HTTP_ACCEPT_LANGUAGE'])) {

                $accept = $this->parseList($this->server['HTTP_ACCEPT_LANGUAGE']);

                // replace unparsed value with parsed value
                $this->server['HTTP_ACCEPT_LANGUAGE'] = $accept;

            } else {

                // use the previously parsed value
                $accept = $this->server['HTTP_ACCEPT_LANGUAGE'];

            }
        }

        return $accept;
    }

    /**
     * @return string
     */
    public function getUserAgent()
    {
        return $this->server['HTTP_USER_AGENT'];
    }

    /**
     * @return string
     */
    public function getServerName()
    {
        return $this->server['SERVER_NAME'];
    }

    /**
     * @return string
     */
    public function getServerAddr()
    {
        return $this->server['SERVER_ADDR'];
    }

    /**
     * @return int
     */
    public function getServerPort()
    {
        return (int)$this->server['SERVER_PORT'];
    }

    /**
     * @return string
     */
    public function getRemoteAddr()
    {
        return $this->server['REMOTE_ADDR'];
    }

    /**
     * @return int
     */
    public function getRemotePort()
    {
        return (int)$this->server['REMOTE_PORT'];
    }

    /**
     * @return string
     */
    public function getAuthUser()
    {
        return $this->server['PHP_AUTH_USER'];
    }

    /**
     * @return string
     */
    public function getAuthPass()
    {
        return $this->server['PHP_AUTH_PASS'];
    }

    /**
     * @return array
     */
    public function getQueryString()
    {
        return $this->server['QUERY_STRING'];
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function get($key = null)
    {
        $value = null;
        if ($key !== null && (isset($this->_get[$key]) || array_key_exists($key, $this->_get))) {
            $value = $this->_get[$key];
        } elseif ($key === null) {
            $value = $this->_get;
        }

        return $value;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function post($key = null)
    {
        $value = null;
        if ($key !== null && (isset($this->_post[$key]) || array_key_exists($key, $this->_post))) {
            $value = $this->_post[$key];
        } elseif ($key === null) {
            $value = $this->_post;
        }

        return $value;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function cookie($key = null)
    {
        $value = null;
        if ($key !== null && (isset($this->_cookie[$key]) || array_key_exists($key, $this->_cookie))) {
            $value = $this->_cookie[$key];
        } elseif ($key === null) {
            $value = $this->_cookie;
        }

        return $value;
    }

    /**
     * @param string $header
     * @return string
     */
    public function getHeader($header)
    {
        $value = null;
        if (is_string($header) && (isset($this->headers[$header]) || array_key_exists($header, $this->headers))) {
            $value = $this->headers[$header];
        }
        return $value;
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function setAttribute($key, $value)
    {
        if (!is_string($key) || empty($key)) {
            throw new \InvalidArgumentException('$key expects non-empty string');
        }
        $this->attributes[$key] = $value;
    }

    /**
     * @param string $key
     */
    public function getAttribute($key)
    {
        $value = null;
        if (isset($this->attributes[$key]) || array_key_exists($key, $this->attributes)) {
            $value = $this->attributes[$key];
        }
        return $value;
    }
}

