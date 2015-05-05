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
    public function getScheme();

    /**
     * @return string
     */
    public function getMethod();

    /**
     * @param string $header
     * @return mixed
     */
    public function getHeader($header);

    /**
     * @return string
     */
    public function getUri();

    /**
     * @return array
     */
    public function getAcceptableMimeTypes();
}

