<?php

/**
 * ResponseInterface.php
 * Copyright (c) 2015
 * @author Matt Ferris <matt@bueller.ca>
 */

namespace MattFerris\HttpRouting;

interface ResponseInterface
{
    /**
     * @param int $code
     * @return ResponseInterface
     */
    public function setCode($cod);

    /**
     * @param string $header
     * @param string $value
     * @return ResponseInterface
     */
    public function setHeader($header, $value);

    /**
     * @param string $type
     * @return ResponseInterface
     */
    public function setContentType($type);

    /**
     * @param string $data
     * @return ResponseInterface
     */
    public function setBody($data);

    public function send();
}

