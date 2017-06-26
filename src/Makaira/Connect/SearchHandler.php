<?php
/**
 * This file is part of a marmalade GmbH project
 * It is not Open Source and may not be redistributed.
 * For contact information please visit http://www.marmalade.de
 * Version:    1.0
 * Author:     Jens Richter <richter@marmalade.de>
 * Author URI: http://www.marmalade.de
 */

namespace Makaira\Connect;

use Makaira\Aggregation;
use Makaira\HttpClient;
use Makaira\Query;
use Makaira\Result;
use Makaira\ResultItem;

class SearchHandler
{
    public function __construct(HttpClient $httpClient, $url)
    {
        $this->httpClient = $httpClient;
        $this->url = rtrim($url,'/') . '/';
    }

    /**
     * @param Query $query
     *
     * @return Result
     */
    public function search(Query $query)
    {
        $request = "{$this->url}search/";
        $body = json_encode($query);
        $response = $this->httpClient->request('POST', $request, $body);

        $result = json_decode($response->body, true);

        if (isset($result['ok']) && $result['ok'] === false) {
            throw new \RuntimeException("Error in makaira: {$result['message']}");
        }

        if (!isset($result['items']) && !isset($result['aggregations'])) {
            throw new \UnexpectedValueException("Invalid response from makaira");
        }

        foreach ($result['items'] as $key => $item) {
            $result['items'][$key] = new ResultItem($item);
        }
        foreach ($result['aggregations'] as $key => $item) {
            $result['aggregations'][$key] = new Aggregation($item);
        }

        return new Result($result);
    }
}
