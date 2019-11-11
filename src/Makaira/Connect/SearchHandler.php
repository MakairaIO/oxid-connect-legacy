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
use Makaira\HttpClient;
use Makaira\Connect\VersionHandler;
use Makaira\Connect\Exception as ConnectException;
use Makaira\Connect\Exceptions\UnexpectedValueException;

class SearchHandler extends AbstractHandler
{
    /**
     * @var VersionHandler
     */
    protected $versionHandler;

    /**
     * SearchHandler constructor.
     *
     * @param HttpClient     $httpClient
     * @param string         $url
     * @param string         $instance
     * @param VersionHandler $versionHandler
     */
    public function __construct(HttpClient $httpClient, $url, $instance, VersionHandler $versionHandler)
    {
        parent::__construct($httpClient, $url, $instance);

        $this->versionHandler = $versionHandler;
    }

    /**
     * @param Query $query
     *
     * @return Result
     */
    public function search(Query $query, $debugTrace = null)
    {
        $query->searchPhrase = htmlspecialchars_decode($query->searchPhrase, ENT_QUOTES);
        $query->apiVersion   = $this->versionHandler->getVersionNumber();
        $request             = "{$this->url}search/";
        $body                = json_encode($query);
        $headers             = ["X-Makaira-Instance: {$this->instance}"];
        if ($debugTrace) {
            $headers[] = "X-Makaira-Trace: {$debugTrace}";
        }
        $response            = $this->httpClient->request('POST', $request, $body, $headers);

        $apiResult = json_decode($response->body, true);

        if (isset($apiResult['ok']) && $apiResult['ok'] === false) {
            throw new ConnectException("Error in makaira: {$apiResult['message']}");
        }

        if (!isset($apiResult['product'])) {
            throw new UnexpectedValueException("Product results missing");
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
            return $data;
        }

        foreach ($data['items'] as $key => $item) {
            $data['items'][$key] = new ResultItem($item);
        }
        foreach ($data['aggregations'] as $key => $item) {
            $data['aggregations'][$key] = new Aggregation($item);
        }

        if (array_key_exists('viewConfiguration', $data)) {
            unset($data['viewConfiguration']);
        }

        return new Result($data);
    }
}
