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
 * Class makaira_connect_search
 */
class makaira_connect_search extends makaira_connect_search_parent
{
    protected $facets = null;
    protected $result;
    protected $additionalResults;

    public function init()
    {
        $isActive = oxRegistry::getConfig()->getShopConfVar(
            'makaira_connect_activate_search',
            null,
            oxConfig::OXMODULE_MODULE_PREFIX . 'makaira/connect'
        );
        if (!$isActive) {
            return parent::init();
        }
        startProfile('searchView');
        return $this->initOxsearch();
        stopProfile('searchView');
    }

    public function getSortIdent()
    {
        return 'search';
    }

    public function getFacet()
    {
        return $this->facets;
    }

    protected function initOxsearch()
    {
        // do not use parent::init() to prevent database query
        // oxubase::init() has to be called statically, otherwise essential smarty _tpl_vars are not available
        oxubase::init();

        $myConfig = $this->getConfig();

        $query = new Query([
            'searchPhrase' => oxRegistry::getConfig()->getRequestParameter('searchparam', true),
        ]);

        // check mysql for product with product number
        $oSearchHandler = oxNew('oxsearch');
        $productNumberProduct = $this->loadProductByNumber($query->searchPhrase);
        if($productNumberProduct instanceof oxArticle) {
            oxRegistry::getUtils()->redirect($productNumberProduct->getLink(), false, 302);
        }

        $query->constraints = array_filter(
            [
                Constraints::SHOP => oxRegistry::getConfig()->getShopId(),
                Constraints::LANGUAGE => oxRegistry::getLang()->getLanguageAbbr(),
                Constraints::USE_STOCK => oxRegistry::getConfig()->getShopConfVar('blUseStock'),
                Constraints::CATEGORY   => rawurldecode(oxRegistry::getConfig()->getRequestParameter('searchcnid')),
                Constraints::MANUFACTURER => rawurldecode(oxRegistry::getConfig()->getRequestParameter('searchmanufacturer')),
                'vendor'       => rawurldecode(oxRegistry::getConfig()->getRequestParameter('searchvendor')),

            ]
        );
        $query->aggregations = array_filter(
            $this->getViewConfig()->getFacetParams()
        );
        $sorting = $this->getSorting($this->getSortIdent());
        $query->sorting = $this->sanitizeSorting($sorting);

        list($displayedProducts, $offset) = $this->getLimitOffset();

        $query->count = $displayedProducts;
        $query->offset = $offset;

        $this->_blEmptySearch = false;
        if ($this->isSearchEmpty($query)) {
            //no search string
            $this->_aArticleList = null;
            $this->_blEmptySearch = true;

            return;
        }

        $oSearchList = $this->getProductsFromMakaira($query);

        // list of found articles
        $this->_aArticleList = $oSearchList;
        $this->_iAllArtCnt = 0;

        // skip count calculation if no articles in list found
        if ($oSearchList->count()) {
            $this->_iAllArtCnt = $this->getProductCount($query);
        } else {
            $this->_aArticleList = null;
            // Do not set search empty because we might have hits in additional types
            //$this->_blEmptySearch = true;
        }

        $displayedProducts = $displayedProducts ? $displayedProducts : 1;
        $this->_iCntPages = round($this->_iAllArtCnt / $displayedProducts + 0.49);

        foreach ((array)$this->additionalResults as $key => $value) {
            $this->_aViewData[$key . '_result'] = $value;
        }
    }

    protected function sanitizeSorting($sorting)
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

    protected function isSearchEmpty(Query $query)
    {
        $isEmptySearch = empty($query->searchPhrase) && empty($query->aggregations) && empty($query->contraints);

        return $isEmptySearch;
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
        $displayedProducts = (int) $this->getConfig()->getConfigParam('iNrofCatArticles');
        $displayedProducts = $displayedProducts ? $displayedProducts : 10;
        $offset            = $displayedProducts * $currentPage;

        return array($displayedProducts, $offset);
    }

    protected function getProductsFromMakaira(Query $query)
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

    protected function getProductCount(Query $query)
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
    protected function loadProductByNumber($number) {
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
