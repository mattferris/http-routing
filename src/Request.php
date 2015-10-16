<?php

/**
 * HttpRouting - An HTTP routing dispatcher
 * www.bueller.ca/http-routing
 *
 * Request.php
 * @copyright Copyright (c) 2015
 * @author Matt Ferris <matt@bueller.ca>
 *
 * Licensed under BSD 2-clause license
 * www.bueller.ca/http-routing/license
 */

namespace MattFerris\HttpRouting;

class Request implements RequestInterface
{
    /**
     * @var array Server values (typically from $_SERVER)
     */
    protected $server = array();

    /**
     * @var array Query string parameters (typically from $_GET)
     */
    protected $_get = array();

    /**
     * @var array Posted form values (typically from $_POST)
     */
    protected $_post = array();

    /**
     * @var array Cookies sent by the client (typically from $_COOKIES)
     */
    protected $_cookie = array();

    /**
     * @var array Request headers
     */
    protected $headers = array();

    /**
     * @var array Custom attributes added to the request
     */
    protected $attributes = array();

    /**
     * @param array $server Server values (typically from $_SERVER)
     * @param array $get Query string parameters (typically from $_GET)
     * @param array $post Posted form values (typically from $_POST)
     * @param array $cookie Cookies sent by the client (typically from $_COOKES)
     * @param array $headers Request headers
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
     * Parse a list of values from a header value
     *
     * @param string $list The header value to parse
     * @param bool $keyValue If true, break into key=>value pairs
     * @return array The list of values
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
     * Check whether the request was made using HTTPS
     *
     * @return bool True if an HTTPS request, otherwise false
     */
    public function isHttps()
    {
        return !empty($this->server['HTTPS']);
    }

    /**
     * Get the HTTP method used in the request
     *
     * @return string The HTTP method used
     */
    public function getMethod()
    {
        return $this->server['REQUEST_METHOD'];
    }

    /**
     * Get the request URI
     *
     * @return string The request URI
     */
    public function getUri()
    {
        return $this->server['REQUEST_URI'];
    }

    /**
     * Get the value of the Host header
     *
     * @return string The value of the Host header
     */
    public function getHost()
    {
        return $this->server['HTTP_HOST'];
    }

    /**
     * Get the value of the Connection header
     *
     * @return string The value of the Connectoin header
     */
    public function getConnection()
    {
        return $this->server['HTTP_CONNECTION'];
    }

    /**
     * Get the value of the Cache-Control header as an array
     *
     * @return array The parsed values in the Cache-Control header
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
     * Get the value of the Accept header as an array
     *
     * @return array The parsed values in the Accept header
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
     * Get the value of the Accept-Encoding header as an array
     *
     * @return array The parsed values in the Accept-Encoding header
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
     * Get the value of the Accept-Language header as an array
     *
     * @return array The parsed values in the Accept-Language header
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
     * Get the value of the User-Agent header
     *
     * @return string The value of the User-Agent header
     */
    public function getUserAgent()
    {
        return $this->server['HTTP_USER_AGENT'];
    }

    /**
     * Get the server name
     *
     * @return string The server name
     */
    public function getServerName()
    {
        return $this->server['SERVER_NAME'];
    }

    /**
     * Get the server address
     *
     * @return string The server address
     */
    public function getServerAddr()
    {
        return $this->server['SERVER_ADDR'];
    }

    /**
     * Get the server port
     *
     * @return int The server port
     */
    public function getServerPort()
    {
        return (int)$this->server['SERVER_PORT'];
    }

    /**
     * Get the remote address
     *
     * @return string The remote address
     */
    public function getRemoteAddr()
    {
        return $this->server['REMOTE_ADDR'];
    }

    /**
     * Get the remote port
     *
     * @return int The remote port
     */
    public function getRemotePort()
    {
        return (int)$this->server['REMOTE_PORT'];
    }

    /**
     * Get the user provided by HTTP authentication
     *
     * @return string The HTTP auth user
     */
    public function getAuthUser()
    {
        return $this->server['PHP_AUTH_USER'];
    }

    /**
     * Get the password provided by HTTP authentication
     *
     * @return string The HTTP auth password
     */
    public function getAuthPass()
    {
        return $this->server['PHP_AUTH_PASS'];
    }

    /**
     * Get the query string as an array
     *
     * @return array The query string
     */
    public function getQueryString()
    {
        return $this->server['QUERY_STRING'];
    }

    /**
     * Get the value of the query string parameter, $key
     *
     * @param string $key The parameter to return
     * @return mixed|null The value of the parameter $key, or null if $key
     *     doesn't exist
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
     * Get the value of the POST'd parameter, $key
     *
     * @param string $key The parameter to return
     * @return mixed|null The value of the parameter $key, or null if $key
     *     doesn't exist
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
     * Get the value of the cookie named $key
     *
     * @param string $key The name of the cookie
     * @return mixed|null The value of the cookie $key, or null if the
     *     cookie doesn't exist
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
     * Get the value of the specified header
     *
     * @param string $header The header to return the value of
     * @return string|null The value of the header, or null if the header
     *     doesn't exist
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
     * Set the value of the specified attribute
     *
     * @param string $key The attribute to set the value of
     * @param mixed $value The value of the attribute
     * @throws \InvalidArgumentException The passed value for $key was invalid
     */
    public function setAttribute($key, $value)
    {
        if (!is_string($key) || empty($key)) {
            throw new \InvalidArgumentException('$key expects non-empty string');
        }
        $this->attributes[$key] = $value;
    }

    /**
     * Get the value of the specified attribute
     *
     * @param string $key The attribute to get the value of
     * @return mixed|null The value of the attribute, or null if it doesn't exist
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

