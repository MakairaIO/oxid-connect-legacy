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
                $sanitizedSorting =
                    [(0 === stripos($sortField, 'OX')) ? strtoupper($sortField) : $sortField => $sortDirection];
        }

        return $sanitizedSorting;
    }

    public function getProductsFromMakaira(Query $query)
    {
        $useCategoryInheritance = oxRegistry::getConfig()->getShopConfVar(
            'makaira_connect_category_inheritance',
            null,
            oxConfig::OXMODULE_MODULE_PREFIX . 'makaira/connect'
        );
        $categoryTreeId         = oxRegistry::getConfig()->getShopConfVar(
            'makaira_connect_categorytree_id',
            null,
            oxConfig::OXMODULE_MODULE_PREFIX . 'makaira/connect'
        );

        $myQuery = clone($query);
        if ($useCategoryInheritance && $categoryTreeId) {
            if (isset($query->aggregations[ $categoryTreeId ])) {
                $_categoryIds = [];
                foreach ($query->aggregations[ $categoryTreeId ] as $_categoryId) {
                    $oCategory = oxNew('oxcategory');
                    $oCategory->load($_categoryId);
                    if ($oCategory) {
                        $_categoryIds[ $_categoryId ] = oxDb::getDb()->getCol(
                            "SELECT OXID FROM oxcategories WHERE OXROOTID = ? AND OXLEFT > ? AND OXRIGHT < ?",
                            [
                                $oCategory->oxcategories__oxrootid->value,
                                $oCategory->oxcategories__oxleft->value,
                                $oCategory->oxcategories__oxright->value,
                            ]
                        );
                    }
                }
                foreach ($_categoryIds as $parentId => $childIds) {
                    if ($intersection = array_intersect($query->aggregations[ $categoryTreeId ], (array) $childIds)) {
                        foreach ($intersection as $childId) {
                            unset($_categoryIds[ $childId ]);
                        }
                    }
                }
                $categoryIds                            = array_unique(array_keys($_categoryIds));
                $query->aggregations[ $categoryTreeId ] = $categoryIds;
                if ($useCategoryInheritance) {
                    foreach ($_categoryIds as $parentId => $childIds) {
                        $categoryIds = array_merge($categoryIds, (array) $childIds);
                    }
                    $categoryIds                            = array_unique($categoryIds);
                    $query->aggregations[ $categoryTreeId ] = $categoryIds;
                }
            }
        }

        /** @var oxArticleList $oxArticleList */
        $oxArticleList = oxNew('oxarticlelist');

        // Hook for request modification
        $this->modifyRequest($query);

        $dic = oxRegistry::get('yamm_dic');
        /** @var SearchHandler $searchHandler */
        $searchHandler = $dic['makaira.connect.searchhandler'];

        $this->result = $searchHandler->search($query);

        $productResult = $this->result['product'];

        $productIds = [];
        foreach ($productResult->items as $item) {
            $productIds[] = $item->id;
        }

        $this->afterSearchRequest($productIds);

        $oxArticleList->loadIds($productIds);
        $oxArticleList->sortByIds($productIds);

        $aggregations = $productResult->aggregations;
        foreach ($aggregations as $aggregation) {
            switch ($aggregation->type) {
                case 'range_slider_custom_1':
                    // fallthrough intentional
                case 'range_slider_custom_2':
                    // fallthrough intentional
                case 'range_slider':
                    // Equal min and max values are not allowed
                    $from = $aggregation->min;
                    $to   = $aggregation->max;
                    if ($from == $to) {
                        unset($aggregations[ $aggregation->key ]);
                        continue;
                    }
                    if (isset($query->aggregations[ $aggregation->key . '_from' ]) ||
                        isset($query->aggregations[ $aggregation->key . '_to' ])) {
                        if (isset($query->aggregations[ $aggregation->key . '_from' ])) {
                            $from = $query->aggregations[ $aggregation->key . '_from' ];
                        }
                        if (isset($query->aggregations[ $aggregation->key . '_to' ])) {
                            $to = $query->aggregations[ $aggregation->key . '_to' ];
                        }
                        $aggregations[ $aggregation->key ]->selectedValues['from'] = $from;
                        $aggregations[ $aggregation->key ]->selectedValues['to']   = $to;
                    }

                    break;
                case 'categorytree':
                    // TODO: find better way to convert multi-array to multi-stdobject
                    $aggregations[ $aggregation->key ]->values =
                        json_decode(json_encode($aggregations[ $aggregation->key ]->values));

                    if ($useCategoryInheritance &&
                        $categoryTreeId &&
                        isset($query->aggregations[ $aggregation->key ])) {
                        $aggregations[ $aggregation->key ]->selectedValues = $myQuery->aggregations[ $aggregation->key ];

                    } else {
                        $aggregations[ $aggregation->key ]->selectedValues =
                            isset($query->aggregations[ $aggregation->key ]) ? $query->aggregations[ $aggregation->key ] :
                                [];
                    }

                    $this->mapCategoryTitle(
                        $aggregations[ $aggregation->key ]->values,
                        $aggregations[ $aggregation->key ]->selectedValues
                    );

                    break;
                default:
                    $aggregations[ $aggregation->key ]->values         = array_map(
                        function ($value) use ($aggregation, $query) {
                            $valueObject           = new stdClass();
                            $valueObject->key      = $value['key'];
                            $valueObject->count    = $value['count'];
                            $valueObject->selected = false;
                            if (isset($query->aggregations[ $aggregation->key ])) {
                                $valueObject->selected =
                                    in_array($valueObject->key, (array) $query->aggregations[ $aggregation->key ]);
                            }
                            return $valueObject;
                        },
                        $aggregation->values
                    );
                    $aggregations[ $aggregation->key ]->selectedValues =
                        isset($query->aggregations[ $aggregation->key ]) ? $query->aggregations[ $aggregation->key ] :
                            [];
            }
        }
        $this->aggregations = $aggregations;

        //        if ($categoryTreeId) {
        //            if (isset($this->aggregations[$categoryTreeId])) {
        //                if (isset($query->aggregations[$categoryTreeId])) {
        //                    foreach ($query->aggregations[$categoryTreeId] as $key => $cat) {
        //                        if (isset($this->aggregations[$categoryTreeId]->selectedValues[$key])) {
        //                            $query->aggregations[$categoryTreeId][$key] = $this->aggregations[$categoryTreeId]->selectedValues[$key];
        //                        }
        //                    }
        //                }
        //            }
        //        }

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
                $filteredArray[ $type ] = $result;
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
            $pageNumber    = (int) $oxUtilsServer->getOxCookie('makairaPageNumber');
        }

        return $pageNumber;
    }

    public function deletePageNumberCookie()
    {
        $oxUtilsServer = oxRegistry::get('oxUtilsServer');
        $oxUtilsServer->setOxCookie('makairaPageNumber', '', time() - 3600);
    }

    protected function modifyRequest(Query $query)
    {
        return $query;
    }

    /**
     * @param array $productIds
     */
    public function afterSearchRequest(array $productIds = [])
    {
    }

    public function mapCategoryTitle(&$cats, &$selectedCats)
    {
        if ($cats && $selectedCats) {
            foreach ($cats as $cat) {
                $key = array_search($cat->key, $selectedCats);
                if (false !== $key) {
                    $cat->selected        = true;
                    $selectedCats[ $key ] = $cat->title;
                } else {
                    $cat->selected = false;
                }
                if ($cat->subtree) {
                    $this->mapCategoryTitle($cat->subtree, $selectedCats);
                }
            }
        }
    }
}
