<?php

/**
 * RequestInterface.php
 * Copyright (c) 2015
 * @author Matt Ferris <matt@bueller.ca>
 */

namespace MattFerris\HttpRouting;

interface RequestInterface
{
    /**
     * Get the HTTP method used in the request
     *
     * @return string The HTTP method used
     */
    public function getMethod();

    /**
     * Get the request URI
     *
     * @return string The request URI
     */
    public function getUri();

    /**
     * Get the value of the Host header
     *
     * @return string The value of the Host header
     */
    public function getHost();

    /**
     * Get the value of the Connection header
     *
     * @return string The value of the Connectoin header
     */
    public function getConnection();

    /**
     * Get the value of the Cache-Control header as an array
     *
     * @return array The parsed values in the Cache-Control header
     */
    public function getCacheControl();

    /**
     * Get the value of the Accept header as an array
     *
     * @return array The parsed values in the Accept header
     */
    public function getAccept();

    /**
     * Get the value of the Accept-Encoding header as an array
     *
     * @return array The parsed values in the Accept-Encoding header
     */
    public function getAcceptEncoding();

    /**
     * Get the value of the Accept-Language header as an array
     *
     * @return array The parsed values in the Accept-Language header
     */
    public function getAcceptLanguage();

    /**
     * Get the value of the User-Agent header
     *
     * @return string The value of the User-Agent header
     */
    public function getUserAgent();

    /**
     * Get the server name
     *
     * @return string The server name
     */
    public function getServerName();

    /**
     * Get the server address
     *
     * @return string The server address
     */
    public function getServerAddr();

    /**
     * Get the server port
     *
     * @return int The server port
     */
    public function getServerPort();

    /**
     * Get the remote address
     *
     * @return string The remote address
     */
    public function getRemoteAddr();

    /**
     * Get the remote port
     *
     * @return int The remote port
     */
    public function getRemotePort();

    /**
     * Get the user provided by HTTP authentication
     *
     * @return string The HTTP auth user
     */
    public function getAuthUser();

    /**
     * Get the password provided by HTTP authentication
     *
     * @return string The HTTP auth password
     */
    public function getAuthPass();

    /**
     * Get the value of the query string parameter, $key
     *
     * @param string $key The parameter to return
     * @return mixed|null The value of the parameter $key, or null if $key
     *     doesn't exist
     */
    public function get($key = null);

    /**
     * Get the value of the POST'd parameter, $key
     *
     * @param string $key The parameter to return
     * @return mixed|null The value of the parameter $key, or null if $key
     *     doesn't exist
     */
    public function post($key = null);

    /**
     * Get the value of the cookie named $key
     *
     * @param string $key The name of the cookie
     * @return mixed|null The value of the cookie $key, or null if the
     *     cookie doesn't exist
     */
    public function cookie($key = null);
}

