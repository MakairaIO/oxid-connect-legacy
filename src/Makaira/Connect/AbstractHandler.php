<?php
/**
 * This file is part of a marmalade GmbH project
 * It is not Open Source and may not be redistributed.
 * For contact information please visit http://www.marmalade.de
 *
 * @version 0.1
 * @author  Stefan Krenz <krenz@marmalade.de>
 * @link    http://www.marmalade.de
 */

namespace Makaira\Connect;

use Makaira\HttpClient;

abstract class AbstractHandler
{
    /**
     * @var HttpClient
     */
    protected $httpClient;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $instance;

    /**
     * AbstractHandler constructor.
     *
     * @param HttpClient $httpClient
     * @param string     $url
     * @param string     $instance
     */
    public function __construct(HttpClient $httpClient, $url, $instance)
    {
        $this->httpClient = $httpClient;
        $this->url        = rtrim($url, '/') . '/';
        $this->instance   = $instance;
    }
}
