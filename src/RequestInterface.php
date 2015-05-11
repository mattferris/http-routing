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
     * @return string
     */
    public function getMethod();

    /**
     * @return string
     */
    public function getUri();

    /**
     * @return string
     */
    public function getHost();

    /**
     * @return string
     */
    public function getConnection();

    /**
     * @return array
     */
    public function getCacheControl();

    /**
     * @return array
     */
    public function getAccept();

    /**
     * @return array
     */
    public function getAcceptEncoding();

    /**
     * @return array
     */
    public function getAcceptLanguage();

    /**
     * @return string
     */
    public function getUserAgent();

    /**
     * @return string
     */
    public function getServerName();

    /**
     * @return string
     */
    public function getServerAddr();

    /**
     * @return string
     */
    public function getServerPort();

    /**
     * @return string
     */
    public function getRemoteAddr();

    /**
     * @return string
     */
    public function getRemotePort();

    /**
     * @return string
     */
    public function getAuthUser();

    /**
     * @return string
     */
    public function getAuthPass();

    /**
     * @return mixed
     */
    public function get($key = null);

    /**
     * @return mixed
     */
    public function post($key = null);

    /**
     * @return mixed
     */
    public function cookie($key = null);
}

