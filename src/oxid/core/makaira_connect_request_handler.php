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

/**
 * Class makaira_connect_request_handler
 */
class makaira_connect_request_handler
{
    protected $result;
    protected $additionalResults;
    protected $aggregations;

    public function sanitizeSorting($sorting)
    {
        if (!is_array($sorting)) {
            return [];
        }
        $sortField = $sorting['sortby'];
        $sortDirection = $sorting['sortdir'];

        // fix for category quicksort
        $sortField = preg_replace("/^([^.]+\.)?(.*)$/", "$2", trim($sortField));
        switch ($sortField) {
            case 'none':
                $sanitizedSorting = [];
                break;
            default:
                $sanitizedSorting = [$sortField => $sortDirection];
        }

        return $sanitizedSorting;
    }

    public function getProductsFromMakaira(Query $query)
    {
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

        $productIds = [];
        foreach ($this->result->items as $item) {
            $productIds[] = $item->id;
        }

        $oxArticleList->loadIds($productIds);
        $oxArticleList->sortByIds($productIds);

        $aggregations = $this->result->aggregations;
        foreach ($aggregations as $aggregation) {
            switch ($aggregation->type) {
                case 'range_slider':
                    // Equal min and max values are not allowed
                    if ($aggregation->min == $aggregation->max) {
                        unset($aggregations[$aggregation->key]);
                        continue;
                    }
                    $aggregations[$aggregation->key]->selectedValues['from'] = isset($query->aggregations[$aggregation->key.'_from']) ? $query->aggregations[$aggregation->key.'_from'] : $aggregation->min;
                    $aggregations[$aggregation->key]->selectedValues['to'] = isset($query->aggregations[$aggregation->key.'_to']) ? $query->aggregations[$aggregation->key.'_to'] : $aggregation->max;
                    break;
                default:
                    $aggregations[$aggregation->key]->values = array_map(
                        function ($value) use ($aggregation, $query) {
                            $valueObject = new stdClass();
                            $valueObject->key = key($value);
                            $valueObject->count = current($value);
                            $valueObject->selected = false;
                            if (isset($query->aggregations[$aggregation->key])) {
                                $valueObject->selected = in_array($valueObject->key, (array) $query->aggregations[$aggregation->key]);
                            }
                            return $valueObject;
                        },
                        $aggregation->values
                    );
                    $aggregations[$aggregation->key]->selectedValues = isset($query->aggregations[$aggregation->key]) ? $query->aggregations[$aggregation->key] : [];
            }
        }
        $this->aggregations = $aggregations;

        //FIXME: handle additional search types (category, manufacturer, searchlinks)
        $aAdditionalSearchResults = array();
        /*foreach ($oOxSearch->getActiveAdditionalTypes() as $sType) {
            $aAdditionalSearchResults[$sType] = $oOxSearch->searchAdditionalTypes($paramObject, $sType);
        }*/
        $this->additionalResults = $aAdditionalSearchResults;

        return $oxArticleList;
    }

    public function getAggregations()
    {
        return $this->aggregations;
    }

    public function getAdditionalResults()
    {
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
        return $this->result->total;
    }
}
