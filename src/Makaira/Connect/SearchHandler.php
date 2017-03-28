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
        foreach ($result['items'] as $key => $item) {
            $result['items'][$key] = new ResultItem($item);
        }

        return new Result($result);
    }
}
