<?php
/**
 * This file is part of a marmalade GmbH project
 * It is not Open Source and may not be redistributed.
 * For contact information please visit http://www.marmalade.de
 * Version:    1.0
 * Author:     Jens Richter <richter@marmalade.de>
 * Author URI: http://www.marmalade.de
 */

use Makaira\Aggregation;
use Makaira\Connect\SearchHandler;
use Makaira\Connect\Utils\CategoryInheritance;
use Makaira\Connect\Utils\OperationalIntelligence;
use Makaira\Constraints;
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
        $dic = oxRegistry::get('yamm_dic');

        /** @var OperationalIntelligence $operationalIntelligence */
        $operationalIntelligence = $dic['makaira.connect.operational_intelligence'];
        $operationalIntelligence->apply($query);

        $unmodifiedQuery = clone($query);

        /** @var CategoryInheritance $categoryInheritance */
        $categoryInheritance = $dic['makaira.connect.category_inheritance'];
        $categoryInheritance->applyToAggregation($query);

        $personalizationType = null;
        if (oxRegistry::getConfig()->getShopConfVar(
            'makaira_connect_use_econda',
            null,
            oxConfig::OXMODULE_MODULE_PREFIX . 'makaira/connect'
        )) {
            if (isset($_COOKIE['mak_econda_session'])) {
                $personalizationType                                     = 'econda';
                $query->constraints[ Constraints::PERSONALIZATION_TYPE ] = $personalizationType;
                $econdaData                                              = json_decode($_COOKIE['mak_econda_session']);
                $query->constraints[ Constraints::PERSONALIZATION_DATA ] = $econdaData;
            }
        } elseif (oxRegistry::getConfig()->getShopConfVar(
            'makaira_connect_use_odoscope',
            null,
            oxConfig::OXMODULE_MODULE_PREFIX . 'makaira/connect'
        )) {
            $personalizationType                                     = 'odoscope';
            $query->constraints[ Constraints::PERSONALIZATION_TYPE ] = $personalizationType;

            $token  = oxRegistry::getConfig()->getShopConfVar(
                'makaira_connect_odoscope_token',
                null,
                oxConfig::OXMODULE_MODULE_PREFIX . 'makaira/connect'
            );
            $siteId = oxRegistry::getConfig()->getShopConfVar(
                'makaira_connect_odoscope_siteid',
                null,
                oxConfig::OXMODULE_MODULE_PREFIX . 'makaira/connect'
            );

            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $userIp = $_SERVER['HTTP_X_FORWARDED_FOR'];
                $userIp = preg_replace('/,.*$/', '', $userIp);
            } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $userIp = $_SERVER['HTTP_CLIENT_IP'];
            } else {
                $userIp = $_SERVER['REMOTE_ADDR'];
            }
            if (is_string($userIp)) {
                $userIp = preg_replace('/^(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})$/', '$1.$2.*.*', $userIp);
            } else {
                $userIp = '';
            }

            $userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
            $userAgent = is_string($userAgent) ? $userAgent : '';

            $userRef = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
            $userRef = is_string($userRef) ? $userRef : '';

            $query->constraints[ Constraints::PERSONALIZATION_DATA ] = [
                'token'     => $token,
                'siteid'    => $siteId,
                'osccookie' => $_COOKIE["osc-{$token}"],
                'uip'       => $userIp,
                'uas'       => $userAgent,
                'ref'       => $userRef,
            ];
        }

        // Hook for request modification
        $this->modifyRequest($query);

        /** @var SearchHandler $searchHandler */
        $searchHandler = $dic['makaira.connect.searchhandler'];
        $debugTrace    = oxRegistry::getConfig()->getRequestParameter("mak_debug");

        $requestExperiments = json_decode($_COOKIE['mak_experiments'], true);
        if ($requestExperiments) {
            $query->constraints[Constraints::AB_EXPERIMENTS] = $requestExperiments;
        }

        $this->result = $searchHandler->search($query, $debugTrace);

        if ('odoscope' === $personalizationType) {
            if (isset($this->result['personalization']['oscCookie'])) {
                $cookieValue = $this->result['personalization']['oscCookie'];
                oxRegistry::get(makaira_cookie_utils::class)->setCookie(
                    "osc-{$token}",
                    $cookieValue,
                    oxRegistry::get("oxutilsdate")->getTime() + 86400
                );
            }

            if (isset($this->result['personalization']['oscTrackingGroup'])) {
                $odoscopeTracking['group'] = $this->result['personalization']['oscTrackingGroup'];
            } else {
                $odoscopeTracking['group'] = 'ERROR';
            }

            if (isset($this->result['personalization']['oscTrackingData'])) {
                $odoscopeTracking['data'] = $this->result['personalization']['oscTrackingData'];
            } else {
                $odoscopeTracking['data'] = $token;
            }

            /** @var makaira_tracking_data_generator $trackingDataGenerator */
            $trackingDataGenerator = oxRegistry::get(makaira_tracking_data_generator::class);
            $trackingDataGenerator::setOdoscopeData($odoscopeTracking);
        }

        $productResult = $this->result['product'];

        $productIds = $this->mapResultIDs($productResult->items);

        // Hook for result modification
        $this->afterSearchRequest($productIds);

        $oxArticleList = $this->loadProducts($productIds, $productResult);

        $this->aggregations = $this->postProcessAggregations($productResult->aggregations, $query, $unmodifiedQuery);

        $responseExperiments = isset($this->result['experiments']) ? $this->result['experiments'] : [];

        $oxidViewConfig = oxRegistry::get('oxviewconfig');
        if ($oxidViewConfig instanceof makaira_connect_oxviewconfig) {
            $experiments = [];
            foreach ($responseExperiments as $responseExperiment) {
                $experiments[$responseExperiment['experiment']] = $responseExperiment['variation'];
            }
            $oxidViewConfig->setExperiments($experiments);
        }

        oxRegistry::get('oxUtilsServer')->setOxCookie(
            'mak_experiments',
            json_encode($responseExperiments),
            time() + 15552000 // 180 days
        );

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

    /**
     * @param array  $productIds
     * @param Result $productResult
     *
     * @return oxArticleList|oxarticlelist
     */
    public function loadProducts(array $productIds = [], Result $productResult)
    {
        /** @var oxArticleList $oxArticleList */
        $oxArticleList = oxNew('oxarticlelist');

        $oxArticleList->loadIds($productIds);
        $oxArticleList->sortByIds($productIds);

        return $oxArticleList;
    }

    public function mapCategoryTitle(&$cats, &$selectedCats)
    {
        if ($cats && $selectedCats) {
            foreach ($cats as $cat) {
                $key = array_search($cat->key, (array) $selectedCats);
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

    /**
     * @param array $aggregations
     * @param Query                $query
     * @param Query                $unmodifiedQuery
     *
     * @return array
     */
    protected function postProcessAggregations(array $aggregations, Query $query, Query $unmodifiedQuery)
    {
        foreach ($aggregations as $aggregation) {
            switch ($aggregation->type) {
                case 'range_slider_custom_1':
                    // fallthrough intentional
                case 'range_slider_custom_2':
                    // fallthrough intentional
                case 'range_slider':
                    // Equal min and max values are not allowed
                    if ($aggregation->min == $aggregation->max) {
                        unset($aggregations[$aggregation->key]);
                        continue;
                    }
                    $aggregationFromKey = "{$aggregation->key}_from";
                    $aggregationToKey = "{$aggregation->key}_to";
                    $aggregationHasFrom = isset($query->aggregations[$aggregationFromKey]);
                    $aggregationHasTo = isset($query->aggregations[$aggregationToKey]);

                    if ($aggregationHasFrom || $aggregationHasTo) {
                        $aggregations[$aggregation->key]->selectedValues['from'] = $aggregationHasFrom ?
                            $query->aggregations[$aggregationFromKey] :
                            $aggregation->min;
                        $aggregations[$aggregation->key]->selectedValues['to']   = $aggregationHasTo ?
                            $query->aggregations[$aggregationToKey] :
                            $aggregation->max;
                    }

                    break;
                case 'range_slider_price':
                    // Equal min and max values are not allowed
                    if ($aggregation->min == $aggregation->max) {
                        unset($aggregations[$aggregation->key]);
                        continue;
                    }
                    $aggregationFromKey = "{$aggregation->key}_from_price";
                    $aggregationToKey = "{$aggregation->key}_to_price";
                    $aggregationHasFrom = isset($query->aggregations[$aggregationFromKey]);
                    $aggregationHasTo = isset($query->aggregations[$aggregationToKey]);

                    if ($aggregationHasFrom || $aggregationHasTo) {
                        $aggregations[$aggregation->key]->selectedValues['from'] = $aggregationHasFrom ?
                            $query->aggregations[$aggregationFromKey] :
                            $aggregation->min;
                        $aggregations[$aggregation->key]->selectedValues['to']   = $aggregationHasTo ?
                            $query->aggregations[$aggregationToKey] :
                            $aggregation->max;
                    }

                    break;
                case 'categorytree':
                    // TODO: find better way to convert multi-array to multi-stdobject
                    $aggregations[$aggregation->key]->values =
                        json_decode(json_encode($aggregations[$aggregation->key]->values));

                    $aggregations[$aggregation->key]->selectedValues =
                        isset($query->aggregations[$aggregation->key]) ?
                            $unmodifiedQuery->aggregations[$aggregation->key] : [];

                    $this->mapCategoryTitle(
                        $aggregations[$aggregation->key]->values,
                        $aggregations[$aggregation->key]->selectedValues
                    );

                    break;
                default:
                    $aggregations[$aggregation->key]->values         = array_map(
                        function ($value) use ($aggregation, $query) {
                            $valueObject           = new stdClass();
                            $valueObject->key      = $value['key'];
                            $valueObject->count    = $value['count'];
                            $valueObject->selected = false;
                            if (isset($query->aggregations[$aggregation->key])) {
                                $valueObject->selected = in_array(
                                    strtolower($valueObject->key),
                                    array_map(
                                        function ($element) {
                                            return is_bool($element) ? $element : strtolower($element);
                                        },
                                        (array) $query->aggregations[$aggregation->key]
                                    )
                                );
                            }

                            return $valueObject;
                        },
                        $aggregation->values
                    );
                    $aggregations[$aggregation->key]->selectedValues =
                        isset($query->aggregations[$aggregation->key]) ? $query->aggregations[$aggregation->key] : [];
            }
        }

        return $aggregations;
    }

    /**
     * @param $items
     * @return array
     */
    protected function mapResultIDs($items)
    {
        $productIds = [];
        foreach ($items as $item) {
            $productIds[] = $item->fields['id'];
        }
        return $productIds;
    }
}
