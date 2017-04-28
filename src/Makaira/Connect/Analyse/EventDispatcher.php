<?php
/**
 * This file is part of a marmalade GmbH project
 * It is not Open Source and may not be redistributed.
 * For contact information please visit http://www.marmalade.de
 * Version:    1.0
 * Author:     Jens Richter <richter@marmalade.de>
 * Author URI: http://www.marmalade.de
 */

namespace Makaira\Connect\Analyse;

use Makaira\HttpClient;

/**
 * Class EventDispatcher
 *
 * @package Makaira\Connect\Analyse
 */
class EventDispatcher
{
    /**
     * @var HttpClient
     */
    private $httpClient;

    /**
     * EventDispatcher constructor.
     *
     * @param HttpClient $httpClient
     */
    public function __construct(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @param array $parameters
     */
    public function dispatch(array $parameters)
    {
        $path = [];
        foreach ($parameters as $key => $value) {
            $path[] = "{$key}:{$value}";
        }
        $dataPath = implode('/', $path);
        $trackingUrl = "http://tracking.makaira.io/{$dataPath}";
        $this->httpClient->request('GET', $trackingUrl);
    }
}
