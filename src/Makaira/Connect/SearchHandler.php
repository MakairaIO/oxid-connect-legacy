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
        $request = "{$this->url}search?". http_build_query($query);
        $response = $this->httpClient->request('GET', $request);

        return new Result([
            'items' => [new ResultItem(['id' => 'oiaa81b5e002fc2f73b9398c361c0b97'])],
            'count' => 1,
            'total' => 1,
            'offset' => $query->offset,
            'aggregations' => []
        ]);
    }
}
