<?php

/**
 * AbstractController.php
 * Copyright (c) 2015
 * @author Matt Ferris <matt@bueller.ca>
 */

namespace MattFerris\HttpRouting;

use MattFerris\Di\ContainerInterface;

abstract class AbstractController
{
    /**
     * @var DependencyInjectorInterface
     */
    protected $di;

    /**
     * @params ContainerInterface $di
     */
    public function __construct(ContainerInterface $di)
    {
        $this->di = $di;
    }

    /**
     * @param string $url
     * @param bool $permanent
     * @return ResponseInterface
     */
    protected function redirect($url, $permanent = false)
    {
        $code = $msg = null;

        if ($permanent === true) {
            $code = 301;
            $msg = 'The resource you requested has permanently moved to "'.$url.'"';
        } else {
            $code = 307;
            $msg = 'The resource you requested has temporarily moved to "'.$url.'"';
        }

        $response = new Response();
        $response
            ->setCode($code)
            ->setHeader('Location: '.$url);

        return $response;
    }

    /**
     * @param array $body
     * @param array $headers
     * @param int $code
     */
    protected function response(array $body, array $headers = array(), $code = 200)
    {
        $response = new Response();
        $response
            ->setCode($code)
            ->setBody($body);

        foreach ($headers as $name => $value) {
            $response->setHeader($name, $value);
        }

        return $response;
    }
}

