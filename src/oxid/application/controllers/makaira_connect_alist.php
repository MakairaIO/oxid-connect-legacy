<?php

use Makaira\Constraints;
use Makaira\Query;
use Makaira\Connect\Exception as ConnectException;
use Makaira\Connect\Utils\CategoryInheritance;
use Makaira\Connect\Connect;

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
     * @var
     */
    protected $aggregations;

    /**
     * @var
     */
    protected $makairaSearchResult;

    /**
     * @var bool
     */
    protected $blArticleListLoaded;

    /**
     * Set noindex for filtered pages
     *
     * @return int
     */
    public function noIndex()
    {
        $this->_iViewIndexState = parent::noIndex();
        $oViewConf              = $this->getViewConfig();

        $aggregationFilter = $oViewConf->getAggregationFilter();
        if (!empty($aggregationFilter)) {
            $this->_iViewIndexState = VIEW_INDEXSTATE_NOINDEXFOLLOW;
        }

        return $this->_iViewIndexState;
    }

    /**
     * @param      $sUrl
     * @param      $iPage
     * @param null $iLang
     *
     * @return mixed
     */
    protected function _addPageNrParam($sUrl, $iPage, $iLang = null)
    {
        $baseLink = parent::_addPageNrParam($sUrl, $iPage, $iLang);
        if (!oxRegistry::getUtils()->seoIsActive()) {
            return $baseLink;
        }
        $oxViewConfig = $this->getViewConfig();
        $filterParams = $oxViewConfig->getAggregationFilter();
        if (empty($filterParams)) {
            return $baseLink;
        }

        $link = $oxViewConfig->generateSeoUrlFromFilter($baseLink, $filterParams);

        return $link;
    }

    /**
     * @return bool
     */
    public function getAttributes()
    {
        $isActive = oxRegistry::getConfig()->getShopConfVar(
            'makaira_connect_activate_listing',
            null,
            oxConfig::OXMODULE_MODULE_PREFIX . 'makaira/connect'
        );
        if (!$isActive) {
            return parent::getAttributes();
        }

        // Disable oxid attribute filter
        return false;
    }

    /**
     * @param bool $url
     *
     * @return string
     *
     * @deprecated
     */
    public function getLinkWithCategory($url = false)
    {
        $result   = $this->getActiveCategory()->getLink();
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
     * @return mixed
     */
    public function getAggregations()
    {
        return $this->aggregations;
    }

    /**
     * @return mixed
     */
    public function getAddSeoUrlParams()
    {
        $this->getViewConfig()->savePageNumberToCookie();

        return parent::getAddSeoUrlParams();
    }

    /**
     *
     */
    public function resetMakairaFilter()
    {
        $this->getViewConfig()->resetMakairaFilter('category', $this->getViewConfig()->getActCatId());
    }

    /**
     *
     */
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
        if (null !== $this->_aArticleList || true === $this->blArticleListLoaded) {
            return $this->_aArticleList;
        }

        $isActive = oxRegistry::getConfig()->getShopConfVar(
            'makaira_connect_activate_listing',
            null,
            oxConfig::OXMODULE_MODULE_PREFIX . 'makaira/connect'
        );

        if (!$isActive || false === $this->blArticleListLoaded) {
            $this->_aArticleList       = parent::getArticleList();
            $this->blArticleListLoaded = true;
            return $this->_aArticleList;
        }

        $this->blArticleListLoaded = false;

        if ($oCategory = $this->getActiveCategory()) {
            try {
                // load products from makaira
                $aArticleList = $this->makairaLoadArticles($oCategory);
                if (count($aArticleList)) {
                    $this->_aArticleList = $aArticleList;
                }
                $this->blArticleListLoaded = true;
                $this->addTplParam('isMakairaSearchEnabled', true);
            } catch (ConnectException $e) {
                //$oxException = new oxException($e->getMessage(), $e->getCode());
                //$oxException->debugOut();
            } catch (\Exception $e) {
                $oxException = new oxException($e->getMessage(), $e->getCode());
                $oxException->debugOut();
            }
        }

        return $this->_aArticleList;
    }

    /**
     * Prepares query object, triggers api request and returns product list
     *
     * @param $oCategory
     *
     * @return \oxArticleList
     */
    protected function makairaLoadArticles($oCategory)
    {
        if ($oCategory->isPriceCategory()) {
            return parent::_loadArticles($oCategory);
        }

        if (null !== $this->makairaSearchResult) {
            return $this->makairaSearchResult;
        }

        /** @var makaira_connect_request_handler $requestHelper */
        $requestHelper = oxNew('makaira_connect_request_handler');

        $myConfig = $this->getConfig();

        $limit   = (int) $myConfig->getConfigParam('iNrofCatArticles');
        $limit   = $limit ? $limit : 1;
        $offset  = $limit * $this->_getRequestPageNr();
        $sorting = $this->getSorting($this->getSortIdent());

        // TODO: Is there a reason to not use $oCategory->getId() here?
        $categoryId = oxRegistry::getConfig()->getRequestParameter('cnid');

        $container = Connect::getContainerFactory()->getContainer();
        /** @var CategoryInheritance $categoryInheritance */
        $categoryInheritance = $container->get(CategoryInheritance::class);

        $categoryId          = $categoryInheritance->buildCategoryInheritance($categoryId);

        $query = new Query();

        $query->isSearch = false;

        $query->constraints = array_filter(
            [
                Constraints::SHOP      => $myConfig->getShopId(),
                Constraints::LANGUAGE  => oxRegistry::getLang()->getLanguageAbbr(),
                Constraints::USE_STOCK => $myConfig->getShopConfVar('blUseStock'),
                Constraints::CATEGORY  => $categoryId,
            ]
        );

        $query->aggregations = array_filter(
            $this->getViewConfig()->getAggregationFilter(),
            function ($value) {
                return (bool) $value || ("0" === $value);
            }
        );

        $query->sorting = $requestHelper->sanitizeSorting($sorting);

        $query->count  = $limit;
        $query->offset = $offset;

        $oArtList = $requestHelper->getProductsFromMakaira($query);

        $this->aggregations = $requestHelper->getAggregations();

        $this->_iAllArtCnt = $requestHelper->getProductCount($query);

        //$this->facet = $oArtList->getFacets();
        $this->_iCntPages = round($this->_iAllArtCnt / $limit + 0.49);

        $this->makairaSearchResult = $oArtList;

        $this->modifyViewData($requestHelper);

        return $oArtList;
    }

    /**
     * @param $requestHelper
     */
    protected function modifyViewData($requestHelper)
    {
        return;
    }
}
