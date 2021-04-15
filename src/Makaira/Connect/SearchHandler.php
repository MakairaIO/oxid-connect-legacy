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
     * @var array
     */
    protected $maxItems = [
        'category'     => -1,
        'links'        => -1,
        'manufacturer' => -1,
        'product'      => -1,
        'suggestion'   => -1,
    ];

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
        $headers             = [
            "X-Makaira-Instance: {$this->instance}",
            "Content-Type: application/json; charset=UTF-8"
        ];
        if ($debugTrace) {
            $headers[] = "X-Makaira-Trace: {$debugTrace}";
        }
        $response            = $this->httpClient->request('POST', $request, $body, $headers);

        $apiResult = json_decode($response->body, true);

        if ($response->status !== 200) {
            throw new ConnectException("Connect to '{$request}' failed. HTTP-Status {$response->status}");
        }

        if (isset($apiResult['ok']) && $apiResult['ok'] === false) {
            throw new ConnectException("Error in makaira: {$apiResult['message']}");
        }

        if (!isset($apiResult['product'])) {
            throw new UnexpectedValueException("Product results missing");
        }

        $result                         = [];
        $this->maxItems['category']     = $this->loadConfigParam('makaira_search_results_category');
        $this->maxItems['links']        = $this->loadConfigParam('makaira_search_results_links');
        $this->maxItems['manufacturer'] = $this->loadConfigParam('makaira_search_results_manufacturer');
        $this->maxItems['product']      = $this->loadConfigParam('makaira_search_results_product');
        $this->maxItems['suggestion']   = $this->loadConfigParam('makaira_search_results_suggestion');
        foreach ($apiResult as $documentType => $data) {
            $result[ $documentType ] = $this->parseResult(
                $data,
                isset($this->maxItems[ $documentType ]) ? $this->maxItems[ $documentType ] : -1
            );
        }

        return array_filter($result);
    }

    /**
     * @param $param
     *
     * @return mixed
     */
    protected function loadConfigParam($param)
    {
        $value = (int) \oxRegistry::getConfig()->getShopConfVar(
            $param,
            null,
            \oxConfig::OXMODULE_MODULE_PREFIX . 'makaira/connect'
        );

        if ($value <= 0) {
            $value = -1;
        }

        return $value;
    }

    /**
     * @param mixed $data
     * @param int   $max_items
     *
     * @return Result
     */
    private function parseResult($data, $max_items = -1)
    {
        if (!isset($data['items']) && !isset($data['aggregations'])) {
            return $data;
        }

        $items         = $data['items'];
        $data['items'] = [];
        foreach ($items as $key => $item) {
            if (-1 === $max_items || count($data['items']) < $max_items) {
                $data['items'][ $key ] = new ResultItem($item);
            }
        }
        $data['count'] = count($data['items']);

        foreach ($data['aggregations'] as $key => $item) {
            $data['aggregations'][ $key ] = new Aggregation($item);
        }

        return new Result($data);
    }
}
