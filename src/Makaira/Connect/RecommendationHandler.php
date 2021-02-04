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

use Makaira\Connect\Exceptions\FeatureNotAvailableException;
use Makaira\Connect\Utils\OperationalIntelligence;
use Makaira\RecommendationQuery;
use Makaira\Result;
use Makaira\ResultItem;
use Makaira\Connect\Exception as ConnectException;
use Makaira\Connect\Exceptions\UnexpectedValueException;
use oxRegistry;

class RecommendationHandler extends AbstractHandler
{
    public function recommendation(RecommendationQuery $query, $debugTrace = null)
    {
        $dic = oxRegistry::get('yamm_dic');

        /** @var OperationalIntelligence $operationalIntelligence */
        $operationalIntelligence = $dic['makaira.connect.operational_intelligence'];
        $operationalIntelligence->apply($query);

        $request = "{$this->url}recommendation";
        $body    = json_encode($query);
        $headers = ["X-Makaira-Instance: {$this->instance}", "Content-Type: application/json; charset=UTF-8"];

        if ($debugTrace) {
            $headers[] = "X-Makaira-Trace: {$debugTrace}";
        }

        $response = $this->httpClient->request(
            'POST',
            $request,
            $body,
            $headers
        );

        $apiResult = json_decode($response->body, true);

        if (402 === $response->status) {
            throw new FeatureNotAvailableException("Feature 'recommendations' is not available");
        }
        if (isset($apiResult['ok']) && $apiResult['ok'] === false) {
            throw new ConnectException("Error in makaira: {$apiResult['message']}");
        }

        if (!isset($apiResult['items'])) {
            throw new UnexpectedValueException("Item results missing");
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
