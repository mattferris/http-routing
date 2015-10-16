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
     * Set the HTTP status code
     *
     * @param int $code The HTTP status code
     * @return self
     */
    public function setCode($cod);

    /**
     * Set the value of the specified header
     *
     * @param string $header The name of the header to set
     * @param string $value The value of the header
     * @return self
     */
    public function setHeader($header, $value);

    /**
     * Set the value of the Content-Type header
     *
     * @param string $type The value to set Content-Type header to
     * @return self
     */
    public function setContentType($type);

    /**
     * Set the body of the response
     *
     * @param string $data The body value
     * @return self
     */
    public function setBody($data);

    /**
     * Send the response body using echo() and sending headers using
     * sendHeaders()
     *
     * @see sendHeaders()
     */
    public function send();
}

