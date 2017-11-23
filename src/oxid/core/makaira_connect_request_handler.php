<?php
/**
 * This file is part of a marmalade GmbH project
 * It is not Open Source and may not be redistributed.
 * For contact information please visit http://www.marmalade.de
 * Version:    1.0
 * Author:     Jens Richter <richter@marmalade.de>
 * Author URI: http://www.marmalade.de
 */

use Makaira\Connect\SearchHandler;
use Makaira\Query;
use Makaira\Result;
use Makaira\Constraints;

/**
 * Class makaira_connect_request_handler
 */
class makaira_connect_request_handler
{
    /**
     * @var Result[]
     */
    protected $result;

    /**
     * @var array
     */
    protected $additionalResults;

    /**
     * @var array
     */
    protected $aggregations;

    public function sanitizeSorting($sorting)
    {
        if (!is_array($sorting)) {
            return [];
        }
        $sortField     = $sorting['sortby'];
        $sortDirection = $sorting['sortdir'];

        // fix for category quicksort
        $sortField = preg_replace("/^([^.]+\.)?(.*)$/", "$2", trim($sortField));
        switch ($sortField) {
            case 'none':
                $sanitizedSorting = [];
                break;
            default:
                $sanitizedSorting = [(0 === stripos($sortField, 'OX')) ? strtoupper($sortField) : $sortField => $sortDirection];
        }

        return $sanitizedSorting;
    }

    public function getProductsFromMakaira(Query $query)
    {
        $useUserAgent = oxRegistry::getConfig()->getShopConfVar(
            'makaira_connect_use_user_agent',
            null,
            oxConfig::OXMODULE_MODULE_PREFIX . 'makaira/connect'
        );
        if ($useUserAgent) {
            $query->constraints[ Constraints::USER_OI_ID ]    = $this->getUserOiID();
            $query->constraints[ Constraints::USER_AGENT ]    = $this->getUserAgentString();
            $query->constraints[ Constraints::USER_TIMEZONE ] = $this->getUserTimeZone();
        }

        /** @var oxArticleList $oxArticleList */
        $oxArticleList = oxNew('oxarticlelist');

        //TODO: Refactor or remove customFilter hook
        if (method_exists($oxArticleList, 'getCustomFilters') && is_array($customFilters = $oxArticleList->getCustomFilters())) {
            $query->aggregations = array_merge($query->aggregations, $customFilters);
        }

        $dic = oxRegistry::get('yamm_dic');
        /** @var SearchHandler $searchHandler */
        $searchHandler = $dic['makaira.connect.searchhandler'];

        $this->result = $searchHandler->search($query);

        $productResult = $this->result['product'];

        $productIds = [];
        foreach ($productResult->items as $item) {
            $productIds[] = $item->id;
        }

        $oxArticleList->loadIds($productIds);
        $oxArticleList->sortByIds($productIds);

        $aggregations = $productResult->aggregations;
        foreach ($aggregations as $aggregation) {
            switch ($aggregation->type) {
                case 'range_slider':
                    // Equal min and max values are not allowed
                    if ($aggregation->min == $aggregation->max) {
                        unset($aggregations[$aggregation->key]);
                        continue;
                    }
                    if (isset($query->aggregations[$aggregation->key . '_from'])) {
                        $aggregations[$aggregation->key]->selectedValues['from'] = $query->aggregations[$aggregation->key . '_from'];
                    }
                    if (isset($query->aggregations[$aggregation->key . '_to'])) {
                        $aggregations[$aggregation->key]->selectedValues['to'] = $query->aggregations[$aggregation->key . '_to'];
                    }
                    break;
                default:
                    $aggregations[$aggregation->key]->values         = array_map(
                        function ($value) use ($aggregation, $query) {
                            $valueObject           = new stdClass();
                            $valueObject->key      = key($value);
                            $valueObject->count    = current($value);
                            $valueObject->selected = false;
                            if (isset($query->aggregations[$aggregation->key])) {
                                $valueObject->selected = in_array($valueObject->key, (array)$query->aggregations[$aggregation->key]);
                            }
                            return $valueObject;
                        },
                        $aggregation->values
                    );
                    $aggregations[$aggregation->key]->selectedValues = isset($query->aggregations[$aggregation->key]) ? $query->aggregations[$aggregation->key] : [];
            }
        }
        $this->aggregations = $aggregations;

        return $oxArticleList;
    }

    public function getAggregations()
    {
        return $this->aggregations;
    }

    public function getAdditionalResults()
    {
        if (null !== $this->additionalResults) {
            return $this->additionalResults;
        }

        $filteredArray = [];
        foreach ((array) $this->result as $type => $result) {
            if (('product' !== $type) && ($result->count > 0)) {
                $filteredArray[$type] = $result;
            }
        }

        $this->additionalResults = $filteredArray;

        return $this->additionalResults;
    }

    public function getProductCount(Query $query)
    {
        if (!isset($this->result)) {
            $dic = oxRegistry::get('yamm_dic');
            /** @var SearchHandler $searchHandler */
            $searchHandler = $dic['makaira.connect.searchhandler'];

            $this->result = $searchHandler->search($query);
        }
        return $this->result['product']->total;
    }

    public function getPageNumber($pageNumber = 0)
    {
        if (!$pageNumber) {
            /** @var oxUtilsServer $oxUtilsServer */
            $oxUtilsServer = oxRegistry::get('oxUtilsServer');
            $pageNumber    = (int)$oxUtilsServer->getOxCookie('makairaPageNumber');
        }

        return $pageNumber;
    }

    public function deletePageNumberCookie()
    {
        $oxUtilsServer = oxRegistry::get('oxUtilsServer');
        $oxUtilsServer->setOxCookie('makairaPageNumber', '', time() - 3600);
    }

    /**
     * Get User ID (set cookie "oiID")
     */
    public function getUserOiID()
    {
        /** @var string $userID */
        $userID = isset($_COOKIE['oiID']) ? $_COOKIE['oiID'] : false;

        if (!$userID || !is_string($userID)) {
            $userID = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
            $userID .= $this->getUserAgentString();

            $userID = md5($userID);

            /** @var oxUtilsServer $oxUtilsServer */
            $oxUtilsServer = oxRegistry::get('oxUtilsServer');
            $oxUtilsServer->setOxCookie('oiID', $userID, time() + 86400);
        }

        return $userID;
    }

    /**
     * Get actual User Agent String (raw data)
     */
    public function getUserAgentString()
    {
        /** @var string $userAgent */
        $userAgent = $_SERVER['HTTP_USER_AGENT'];

        return is_string($userAgent) ? $userAgent : '';
    }

    /**
     * Get User Time Zone from cookie "oiLocalTimeZone"
     */
    public function getUserTimeZone()
    {
        /** @var string $userTimeZone */
        $userTimeZone = isset($_COOKIE['oiLocalTimeZone']) ? $_COOKIE['oiLocalTimeZone'] : '';

        return is_string($userTimeZone) ? $userTimeZone : '';
    }
}
