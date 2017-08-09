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
class makaira_connect_alist extends makaira_connect_alist_parent
{
    protected $aggregations;

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
        $result = $this->getActiveCategory()->getLink();
        $category = oxNew('oxcategory');
        if ($category->load(oxRegistry::getConfig()->getRequestParameter('marm_cat')) && !$url) {
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
     * @deprecated
     *
     * @return mixed
     */
    public function getAggregations()
    {
        return $this->aggregations;
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

        if (!$oCategory = $this->getActiveCategory()) {
            return $this->_aArticleList;
        }

        try {
            // load products from makaira
            $aArticleList = $this->makairaLoadArticles($oCategory);
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
    protected function makairaLoadArticles($oCategory)
    {
        if ($oCategory->isPriceCategory()) {
            return parent::_loadArticles($oCategory);
        }

        /** @var makaira_connect_request_handler $requestHelper */
        $requestHelper = oxNew('makaira_connect_request_handler');

        $myConfig = $this->getConfig();

        $limit = (int)$myConfig->getConfigParam('iNrofCatArticles');
        $limit = $limit ? $limit : 1;
        $offset   = $limit * $this->_getRequestPageNr();
        $sorting = $this->getSorting($oCategory->getId());

        // TODO: Is there a reason to not use $oCategory->getId() here?
        $sActCat = oxRegistry::getConfig()->getRequestParameter('cnid');

        $query = new Query();

        $query->isSearch = false;

        $query->constraints = array_filter(
            [
                Constraints::SHOP => $myConfig->getShopId(),
                Constraints::LANGUAGE => oxRegistry::getLang()->getLanguageAbbr(),
                Constraints::USE_STOCK => $myConfig->getShopConfVar('blUseStock'),
                Constraints::CATEGORY   => $sActCat,
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
