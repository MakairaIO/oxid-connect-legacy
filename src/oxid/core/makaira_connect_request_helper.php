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
use Makaira\Constraints;
use Makaira\Query;

/**
 * Class makaira_connect_request_helper
 */
class makaira_connect_request_helper
{
    protected $result;
    protected $additionalResults;

    public function __construct(oxConfig $config, oxViewConfig $viewConfig, $sorting)
    {
        $this->viewConfig = $viewConfig;
        $this->config = $config;
        $this->sorting = $sorting;
    }

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

    /**
     * @return array
     */
    protected function getLimitOffset()
    {
        // sets active page
        $currentPage = (int) oxRegistry::getConfig()->getRequestParameter('pgNr');
        $currentPage = ($currentPage < 0) ? 0 : $currentPage;

        // load only articles which we show on screen
        //setting default values to avoid possible errors showing article list
        $displayedProducts = (int) $this->config->getConfigParam('iNrofCatArticles');
        $displayedProducts = $displayedProducts ? $displayedProducts : 10;
        $offset            = $displayedProducts * $currentPage;

        return array($displayedProducts, $offset);
    }

    public function getProductsFromMakaira(Query $query)
    {
        /** @var oxArticleList $oxArticleList */
        $oxArticleList = oxNew('oxarticlelist');

        //TODO: Refactor or remove customFilter hook
        if (is_array($customFilters = $oxArticleList->getCustomFilters())) {
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
                    $facets[$aggregation->key] = [
                        "min" => $aggregation->min,
                        "max" => $aggregation->max,
                    ];
                    break;
                default:
                    $facets[$aggregation->key] = array_map(
                        function ($value) {
                            return ['key' => key($value), 'doc_count' => current($value)];
                        },
                        $aggregation->values
                    );
            }
        }
        $this->facets = $facets;

        //FIXME: handle additional search types (category, manufacturer, searchlinks)
        $aAdditionalSearchResults = array();
        /*foreach ($oOxSearch->getActiveAdditionalTypes() as $sType) {
            $aAdditionalSearchResults[$sType] = $oOxSearch->searchAdditionalTypes($paramObject, $sType);
        }*/
        $this->additionalResults = $aAdditionalSearchResults;


        return $oxArticleList;
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

    /**
     * Loads single article by it's number
     *
     * @param string $nr
     *
     * @return marm_oxsearch_oxarticle|null
     */
    public function loadProductByNumber($number) {
        $number = oxDb::getDb()->quote($number);
        /** @var oxArticle|marm_oxsearch_oxarticle $oxArticle */
        $oxArticle = oxNew('oxarticle');
        $table = $oxArticle->getViewName();
        $activeSnippet = $oxArticle->getSqlActiveSnippet();
        $sql = "SELECT `OXID` FROM {$table} WHERE {$table}.OXARTNUM = {$number} AND {$activeSnippet}";
        $id = oxDb::getDb()->getOne($sql);
        if ($id && $oxArticle->load($id)) {
            return $oxArticle;
        }

        return null;
    }
}
