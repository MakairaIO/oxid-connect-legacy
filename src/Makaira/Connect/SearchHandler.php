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
use Makaira\Query;
use Makaira\Result;
use Makaira\ResultItem;

class SearchHandler extends AbstractHandler
{
    const API_VERSION = "2018.4";

    /**
     * @param Query $query
     *
     * @return Result
     */
    public function search(Query $query)
    {
        $query->apiVersion = self::API_VERSION;
        $request = "{$this->url}search/";
        $body = json_encode($query);
        $response = $this->httpClient->request('POST', $request, $body, ["X-Makaira-Instance: {$this->instance}"]);

        $apiResult = json_decode($response->body, true);

        if (isset($apiResult['ok']) && $apiResult['ok'] === false) {
            throw new \RuntimeException("Error in makaira: {$apiResult['message']}");
        }

        if (!isset($apiResult['product'])) {
            throw new \UnexpectedValueException("Product results missing");
        }

        $result = [];
        foreach ($apiResult as $documentType => $data) {
            $result[$documentType] = $this->parseResult($data);
        }

        return array_filter($result);
    }

    /**
     * @param $data
     *
     * @return Result
     */
    private function parseResult($data)
    {
        if (!isset($data['items']) && !isset($data['aggregations'])) {
            return null;
        }

        foreach ($data['items'] as $key => $item) {
            $data['items'][$key] = new ResultItem($item);
        }
        foreach ($data['aggregations'] as $key => $item) {
            $data['aggregations'][$key] = new Aggregation($item);
        }

        return new Result($data);
    }
}
