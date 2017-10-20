<?php
use Makaira\Constraints;
use Makaira\Query;

/**
 * This file is part of a marmalade GmbH project
 * It is not Open Source and may not be redistributed.
 * For contact information please visit http://www.marmalade.de
 * Version:    1.0
 * Author:     Jens Richter <richter@marmalade.de>
 * Author URI: http://www.marmalade.de
 */
class makaira_connect_manufacturerlist extends makaira_connect_manufacturerlist_parent
{
    protected $aggregations;

    /**
     * Set noindex for filtered pages
     *
     * @return int
     */
    public function noIndex()
    {
        $this->_iViewIndexState = parent::noIndex();
        $oViewConf = $this->getViewConfig();
        if (!empty($oViewConf->getAggregationFilter())) {
            $this->_iViewIndexState = VIEW_INDEXSTATE_NOINDEXNOFOLLOW;
        }

        return $this->_iViewIndexState;
    }

    /**
     * Template variable getter used in filter templates.
     *
     * @deprecated
     *
     * @param bool $url
     *
     * @return string
     */
    public function getLinkWithCategory($url = false)
    {
        if (!$this->getActManufacturer()) {
            return $url;
        }
        $result = $this->getActManufacturer()->getLink();

        $category = $this->getActCategory();
        if ($category && !$url) {
            $url = $category->getLink();
        }
        if (preg_match('/^[a-zA-Z0-9]+:\/\//', $url)) {
            $url = parse_url($url, PHP_URL_PATH);
            $url = substr($url, 1);
        }
        $result .= $url;

        return $result;
    }

    /**
     * Template variable getter used in filter templates
     *
     * @return mixed
     */
    public function getAggregations()
    {
        return $this->aggregations;
    }

    public function getAddSeoUrlParams()
    {
        $this->getViewConfig()->savePageNumberToCookie();

        return parent::getAddSeoUrlParams();
    }

    public function resetMakairaFilter()
    {
        $this->getViewConfig()->resetMakairaFilter('manufacturer', $this->getViewConfig()->getActManufacturerId());
    }

    public function redirectMakairaFilter()
    {
        $this->getViewConfig()->redirectMakairaFilter($this->getActiveCategory()->getLink());
    }

    /**
     * Template variable getter. Returns category's article list
     *
     * @return array
     */
    public function getArticleList()
    {
        if ($this->_aArticleList !== null) {
            return $this->_aArticleList;
        }

        $isActive = oxRegistry::getConfig()->getShopConfVar(
            'makaira_connect_activate_listing',
            null,
            oxConfig::OXMODULE_MODULE_PREFIX . 'makaira/connect'
        );
        if (!$isActive) {
            return parent::getArticleList();
        }

        if (!($oManufacturerTree = $this->getManufacturerTree()) ||
            !($oManufacturer = $this->getActManufacturer()) ||
            ($oManufacturer->getId() == 'root') ||
            !$oManufacturer->getIsVisible()) {
            return $this->_aArticleList;
        }

        try {
            // load products from makaira
            $aArticleList = $this->makairaLoadArticles($oManufacturer);
            $this->addTplParam('isMakairaSearchEnabled', true);
        }  catch (Exception $e) {
            $oxException = new oxException($e->getMessage(), $e->getCode());
            $oxException->debugOut();
            return parent::getArticleList();
        }

        if (count($aArticleList)) {
            $this->_aArticleList = $aArticleList;
        }

        return $this->_aArticleList;
    }

    /**
     * Prepares query object, triggers api request and returns product list
     *
     * @param $oCategory
     *
     * @return oxArticleList
     */
    protected function makairaLoadArticles($oManufacturer)
    {
        /** @var makaira_connect_request_handler $requestHelper */
        $requestHelper = oxNew('makaira_connect_request_handler');

        $myConfig = $this->getConfig();

        $limit = (int)$myConfig->getConfigParam('iNrofCatArticles');
        $limit = $limit ? $limit : 1;
        $offset   = $limit * $this->_getRequestPageNr();
        $sorting = $this->getSorting($this->getSortIdent());

        $manufacturerId = $oManufacturer->getId();

        $query = new Query();

        $query->isSearch = false;

        $query->constraints = array_filter(
            [
                Constraints::SHOP => $myConfig->getShopId(),
                Constraints::LANGUAGE => oxRegistry::getLang()->getLanguageAbbr(),
                Constraints::USE_STOCK => $myConfig->getShopConfVar('blUseStock'),
                Constraints::MANUFACTURER => $manufacturerId,
            ]
        );

        $query->aggregations = array_filter(
            $this->getViewConfig()->getAggregationFilter()
        );

        $query->sorting = $requestHelper->sanitizeSorting($sorting);

        $query->count = $limit;
        $query->offset = $offset;

        $oArtList = $requestHelper->getProductsFromMakaira($query);

        $this->aggregations = $requestHelper->getAggregations();

        $this->_iAllArtCnt = $requestHelper->getProductCount($query);

        //$this->facet = $oArtList->getFacets();
        $this->_iCntPages = round($this->_iAllArtCnt / $limit + 0.49);

        return $oArtList;
    }
}
