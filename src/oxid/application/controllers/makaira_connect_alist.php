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

        // use makaira
        $aArticleList = $this->makairaLoadArticles($oCategory);
        if (count($aArticleList)) {
            $this->_aArticleList = $aArticleList;
        }

        return $this->_aArticleList;
    }

    public function getFacet()
    {
        return $this->facets;
    }

    protected function makairaLoadArticles($oCategory)
    {
        if ($oCategory->isPriceCategory()) {
            return parent::_loadArticles($oCategory);
        }
        $myConfig = $this->getConfig();

        $requestHelper = oxNew('makaira_connect_request_helper', $myConfig, $this->getViewConfig(), []);


        $limit = (int)$myConfig->getConfigParam('iNrofCatArticles');
        $limit = $limit ? $limit : 1;
        $offset   = $limit * $this->_getRequestPageNr();
        $sortingSql = $this->getSorting($oCategory->getId());

        $aSessionFilter = oxRegistry::getSession()->getVariable('session_attrfilter');

        $sActCat = oxRegistry::getConfig()->getRequestParameter('cnid');

        $query = new Query();

        $query->constraints = array_filter(
            [
                Constraints::SHOP => oxRegistry::getConfig()->getShopId(),
                Constraints::LANGUAGE => oxRegistry::getLang()->getLanguageAbbr(),
                Constraints::USE_STOCK => oxRegistry::getConfig()->getShopConfVar('blUseStock'),
                Constraints::CATEGORY   => $sActCat,
            ]
        );

        $query->aggregations = array_filter(
            $this->getViewConfig()->getFacetParams()
        );

        $query->sorting = $requestHelper->sanitizeSorting($sortingSql);

        $query->count = $limit;
        $query->offset = $offset;

        $oArtList = $requestHelper->getProductsFromMakaira($query);

        $this->_iAllArtCnt = $requestHelper->getProductCount($query);

        //$this->facet = $oArtList->getFacets();
        $this->_iCntPages = round($this->_iAllArtCnt / $limit + 0.49);

        return $oArtList;
    }
}
