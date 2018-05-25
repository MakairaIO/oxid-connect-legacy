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

use Makaira\Aggregation;
use Makaira\RecommendationQuery;
use Makaira\Result;
use Makaira\ResultItem;

class RecommendationHandler extends AbstractHandler
{
    public function recommendation(RecommendationQuery $query)
    {
        $request  = "{$this->url}recommendation";
        $body     = json_encode($query);
        $response = $this->httpClient->request(
            'POST',
            $request,
            $body,
            ["X-Makaira-Instance: {$this->instance}"]
        );

        $apiResult = json_decode($response->body, true);

        if (isset($apiResult['ok']) && $apiResult['ok'] === false) {
            throw new \RuntimeException("Error in makaira: {$apiResult['message']}");
        }

        if (!isset($apiResult['items'])) {
            throw new \UnexpectedValueException("Item results missing");
        }

        return $this->parseResult($apiResult);
    }

    /**
     * @param $data
     *
     * @return Result
     */
    private function parseResult($data)
    {
        if (!isset($data['items'])) {
            return null;
        }

        foreach ($data['items'] as $key => $item) {
            $data['items'][$key] = new ResultItem($item);
        }

        return new Result($data);
    }
}
