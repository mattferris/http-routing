<?php

namespace MattFerris\HttpRouting;

class InvalidHeaderException extends \Exception
{
    public function __construct($header)
    {
        parent::__construct('invalid header specified in route criteria: '.$header);
    }
}
