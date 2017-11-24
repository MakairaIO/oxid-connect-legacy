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
                $sanitizedSorting = [(0 === stripos($sortField, 'OX')) ? strtoupper($sortField) : $sortField => $sortDirection];
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
                case 'categorytree':
                    // fallback to categorylist for PHP < 5.6
                    if (version_compare(PHP_VERSION, '5.6', 'gt')) {
                        $paths = array_map(
                            function ($item) {
                                return explode('//', $item['path']);
                            },
                            $aggregation->values
                        );
                        $categoryNames = $this->getCategoryNames(array_merge(...$paths));
                        $selectedValues =
                            isset($query->aggregations[$aggregation->key]) ?
                                $query->aggregations[$aggregation->key] : [];
                        foreach ($paths as $path) {
                            $treePath[]     = $this->buildTreePath($path, $categoryNames, $selectedValues);
                        }

                        $tree = array_merge_recursive(...$treePath);

                        foreach ($tree as $root => $branch) {
                            $tree[$root] = $this->buildTree($root, $branch, $categoryNames, $selectedValues);
                        }

                        $aggregations[$aggregation->key]->values = $tree;
                        $aggregations[$aggregation->key]->selectedValues = isset($query->aggregations[$aggregation->key]) ? $query->aggregations[$aggregation->key] : [];
                        break;
                    }
                case 'categorylist':
                    $categoryIds = array_map(
                        function ($item) {
                            return $item['key'];
                        },
                        $aggregation->values
                    );
                    $categoryNames = $this->getCategoryNames($categoryIds);
                    $aggregations[$aggregation->key]->values         = array_map(
                        function ($value) use ($aggregation, $query, $categoryNames) {
                            $valueObject           = new stdClass();
                            $valueObject->key      = $value['key'];
                            $valueObject->title    = isset($categoryNames[$valueObject->key]) ? $categoryNames[$valueObject->key] : $valueObject->key;
                            $valueObject->count    = $value['count'];
                            $valueObject->selected = false;
                            if (isset($query->aggregations[$aggregation->key])) {
                                $valueObject->selected = in_array($valueObject->key, (array)$query->aggregations[$aggregation->key]);
                            }
                            return $valueObject;
                        },
                        $aggregation->values
                    );
                    $aggregations[$aggregation->key]->selectedValues = isset($query->aggregations[$aggregation->key]) ? $query->aggregations[$aggregation->key] : [];
                    break;
                default:
                    $aggregations[$aggregation->key]->values         = array_map(
                        function ($value) use ($aggregation, $query) {
                            $valueObject           = new stdClass();
                            $valueObject->key      = $value['key'];
                            $valueObject->count    = $value['count'];
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

    private function getCategoryNames($parameters)
    {
        $dic = oxRegistry::get('yamm_dic');
        /** @var \Makaira\Connect\DatabaseInterface $database */
        $database = $dic['oxid.database'];

        $inQuery    = implode(',', array_fill(0, count($parameters), '?'));

        // Fixing array index for binding values
        // @see https://stackoverflow.com/a/5374217
        array_unshift($parameters, 'placeholder');
        unset($parameters[0]);

        $result  = $database->query(
            "SELECT OXID, OXTITLE FROM oxcategories WHERE OXID IN ({$inQuery})",
            $parameters
        );

        $titleMap = [];
        foreach ($result as $item) {
            $titleMap[$item['OXID']] = $item['OXTITLE'];
        }

        return $titleMap;
    }

    private function buildTreePath(array $hierarchy, array $categoryNames, $selectedValues)
    {
        if (empty($hierarchy)) {
            return [];
        }

        $key   = array_shift($hierarchy);
        $subTree = $this->buildTreePath($hierarchy, $categoryNames, $selectedValues);

//        $object = new stdClass();
//        $object->key = $key;
//        $object->title = isset($categoryNames[$key]) ? $categoryNames[$key] : $key;
//        $object->selected = false;
//        $object->subtree = $subTree;
//        if (!empty($selectedValues)) {
//            $object->selected = in_array($object->key, (array) $selectedValues);
//        }

        return [
            $key => $subTree
        ];

//        return $object;
    }

    private function buildTree($key, $subTree, array $categoryNames, $selectedValues)
    {
        $object = new stdClass();
        $object->key = $key;
        $object->title = isset($categoryNames[$key]) ? $categoryNames[$key] : $key;
        $object->selected = false;
        if (!empty($selectedValues)) {
            $object->selected = in_array($object->key, (array) $selectedValues);
        }

        if (!empty($subTree)) {
            foreach ($subTree as $root => $branch) {
                $subTree[$root] = $this->buildTree($root, $branch, $categoryNames, $selectedValues);
            }

            $object->subtree = $subTree;
        }

        return $object;
    }
}
