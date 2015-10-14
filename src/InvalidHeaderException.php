<?php

/**
 * HttpRouting - An HTTP routing dispatcher
 * www.bueller.ca/htt-routing
 *
 * InvalidHeaderException.php
 * @copyright Copyright (c) 2015
 * @author Matt Ferris <matt@bueller.ca>
 *
 * Licensed under BSD 2-clause license
 * www.bueller.ca/http-routing/license
 */

namespace MattFerris\HttpRouting;

class InvalidHeaderException extends \Exception
{
    /**
     * @param string $header The invalid header that was specified
     */
    public function __construct($header)
    {
        parent::__construct('invalid header specified in route criteria: '.$header);
    }
}
