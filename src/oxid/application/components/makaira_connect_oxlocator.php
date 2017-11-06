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
class makaira_connect_oxlocator extends makaira_connect_oxlocator_parent
{
    protected $useCategoryInheritance = null;

    /**
     * Executes locator method according locator type
     *
     * @param oxarticle $oCurrArticle   current article
     * @param oxubase   $oLocatorTarget oxubase object
     */
    public function setLocatorData($oCurrArticle, $oLocatorTarget)
    {
        if ($oLocatorTarget instanceof Details) {
            if ('list' === $this->_sType) {
                $categoryId     = oxRegistry::get('oxViewConfig')->getActCatId();
                $activeCategory = $oLocatorTarget->getActiveCategory();

                if ($activeCategory && ($categoryId !== $activeCategory->getId()) && $this->useCategoryInheritance()) {
                    $oCategoryTree = oxNew('oxCategoryList');
                    $oCategoryTree->buildTree($categoryId);
                    $oLocatorTarget->setCategoryTree($oCategoryTree);
                }
                if (($oCatTree = $oLocatorTarget->getCategoryTree())) {
                    $oLocatorTarget->setCatTreePath($oCatTree->getPath());
                }
            }

            if ('manufacturer' === $this->_sType) {
                if (($oManufacturerTree = $oLocatorTarget->getManufacturerTree())) {
                    $oLocatorTarget->setCatTreePath($oManufacturerTree->getPath());
                }
            }

            return;
        }
        /** @var makaira_connect_request_handler $requestHelper */
        $requestHelper = oxNew('makaira_connect_request_handler');
        $iPage         = $requestHelper->getPageNumber($oLocatorTarget->getActPage());
        $addParams     = '';
        $isSearch      = false;
        $query         = new Query();

        switch ($this->_sType) {
            case 'list':
                $isActive = oxRegistry::getConfig()->getShopConfVar(
                    'makaira_connect_activate_listing',
                    null,
                    oxConfig::OXMODULE_MODULE_PREFIX . 'makaira/connect'
                );
                if (!$isActive) {
                    return parent::setLocatorData($oCurrArticle, $oLocatorTarget);
                }
                $locatorObject = $oLocatorTarget->getActiveCategory();
                if (!$locatorObject) {
                    return;
                }
                $categoryIds = $this->getInheritedCategoryIds($locatorObject);
                break;
            case 'search':
                $isActive = oxRegistry::getConfig()->getShopConfVar(
                    'makaira_connect_activate_search',
                    null,
                    oxConfig::OXMODULE_MODULE_PREFIX . 'makaira/connect'
                );
                if (!$isActive) {
                    return parent::setLocatorData($oCurrArticle, $oLocatorTarget);
                }
                $isSearch            = true;
                $query->searchPhrase = oxRegistry::getConfig()->getRequestParameter('searchparam', true);
                $locatorObject       = $oLocatorTarget->getActSearch();
                if (!$locatorObject) {
                    return;
                }
                $addParams = $this->getSearchAddParams();
                break;
            case 'manufacturer':
                $isActive = oxRegistry::getConfig()->getShopConfVar(
                    'makaira_connect_activate_listing',
                    null,
                    oxConfig::OXMODULE_MODULE_PREFIX . 'makaira/connect'
                );
                if (!$isActive) {
                    return parent::setLocatorData($oCurrArticle, $oLocatorTarget);
                }
                $locatorObject = $oLocatorTarget->getActManufacturer();
                if (!$locatorObject) {
                    return;
                }
                $manufacturerId = $locatorObject->getId();
                break;
            default:
                return parent::setLocatorData($oCurrArticle, $oLocatorTarget);
        }

        $query->enableAggregations = false;

        $sorting = null;
        if ($oLocatorTarget->showSorting()) {
            $sorting = $oLocatorTarget->getSorting($oLocatorTarget->getSortIdent());
        }
        $query->sorting = $requestHelper->sanitizeSorting($sorting);

        $iNrofCatArticles = (int) $this->getConfig()->getConfigParam('iNrofCatArticles');
        $limit            = $iNrofCatArticles + 1;
        $offset           = 0;
        if ($iPage > 0) {
            $offset = ($iPage * $iNrofCatArticles) - 1;
            $limit  = $limit + 1;
        }
        $query->count    = $limit;
        $query->offset   = $offset;
        $query->isSearch = $isSearch;

        $constraints = [
            Constraints::SHOP      => oxRegistry::getConfig()->getShopId(),
            Constraints::LANGUAGE  => oxRegistry::getLang()->getLanguageAbbr(),
            Constraints::USE_STOCK => oxRegistry::getConfig()->getShopConfVar('blUseStock'),
        ];
        if (isset($categoryIds)) {
            $constraints = array_merge($constraints, [Constraints::CATEGORY => $categoryIds]);
        }
        if (isset($manufacturerId)) {
            $constraints = array_merge($constraints, [Constraints::MANUFACTURER => $manufacturerId]);
        }
        $query->constraints = array_filter($constraints);

        $query->aggregations = array_filter(
            oxRegistry::get('oxViewConfig')->getAggregationFilter()
        );

        $idList = $requestHelper->getProductsFromMakaira($query);

        $locatorObject->iCntOfProd = $requestHelper->getProductCount($query);

        $iPos = $this->_getProductPos($oCurrArticle, $idList, $oLocatorTarget);
        if ($iPage > 0) {
            $iPos--;
            $offset++;
        }
        if (1 > $iPos) {
            $requestHelper->deletePageNumberCookie();
            $iPage = 0;
            $offset = 0;
            $query->count            = $iNrofCatArticles + 1;
            $query->offset           = $offset;
            $idList = $requestHelper->getProductsFromMakaira($query);
            $iPos = $this->_getProductPos($oCurrArticle, $idList, $oLocatorTarget);
        }

        if ($locatorObject instanceof oxCategory) {
            $this->setCategoryToListLink($locatorObject, $iPage, $requestHelper);
        } elseif ($locatorObject instanceof oxManufacturer) {
            $isSeoActive = oxRegistry::get('oxUtils')->seoIsActive();
            if (!$isSeoActive) {
                $addParams = 'listtype=manufacturer&amp;mnid=' . $manufacturerId;
            }
            $this->setManufacturerToListLink($locatorObject, $iPage, $isSeoActive);
        } elseif ('search' == $this->_sType) {
            $sPageNr                   = $this->_getPageNumber($iPage);
            $sParams                   = $sPageNr . ($sPageNr ? '&amp;' : '') . $addParams;
            $locatorObject->toListLink = $this->_makeLink($locatorObject->link, $sParams);
        }

        $locatorObject->iProductPos = $iPos + $offset;

        $this->setPrevNextLinks($locatorObject, $iPage, $iNrofCatArticles, $iPos, $addParams);

        $oLocatorTarget->setActiveCategory($locatorObject);
    }

    /**
     * @param oxCategory $category
     */
    private function getInheritedCategoryIds($category)
    {
        $useCategoryInheritance = $this->useCategoryInheritance();

        $categoryIds = (array) $category->getId();

        if ($category && $useCategoryInheritance) {
            $result      = oxDb::getDb()->getCol(
                "SELECT OXID FROM oxcategories WHERE OXROOTID = ? AND OXLEFT > ? AND OXRIGHT < ?",
                [
                    $category->oxcategories__oxrootid->value,
                    $category->oxcategories__oxleft->value,
                    $category->oxcategories__oxright->value,
                ]
            );
            $categoryIds = array_merge(
                (array) $categoryIds,
                $result
            );
        }

        return $categoryIds;
    }

    /**
     * Set to list link
     *
     * @param oxCategory $oCategory
     * @param int        $iPage
     *
     * @return void
     */
    private function setCategoryToListLink(oxCategory $oCategory, $iPage)
    {
        $isSeoActive = oxRegistry::get('oxUtils')->seoIsActive();
        if ($isSeoActive && $iPage) {
            $categoryPageUrl = oxRegistry::get('oxSeoEncoderCategory')->getCategoryPageUrl($oCategory, $iPage);
        } else {
            $categoryPageUrl = $this->_makeLink($oCategory->getLink(), $this->_getPageNumber($iPage));
        }
        if ($isSeoActive) {
            $oxViewConfig    = oxNew('oxViewConfig');
            $filterParams    = $oxViewConfig->getAggregationFilter();
            $categoryPageUrl = $oxViewConfig->generateSeoUrlFromFilter($categoryPageUrl, $filterParams);
        }
        $oCategory->toListLink = $categoryPageUrl;
    }

    /**
     * Set to list link
     *
     * @param oxManufacturer $oxManufacturer
     * @param int            $iPage
     * @param bool           $isSeoActive
     *
     * @return void
     */
    private function setManufacturerToListLink(oxManufacturer $oxManufacturer, $iPage, $isSeoActive = false)
    {
        if ($isSeoActive && $iPage) {
            $manufacturerPageUrl =
                oxRegistry::get('oxSeoEncoderManufacturer')->getManufacturerPageUrl($oxManufacturer, $iPage);
        } else {
            $manufacturerPageUrl = $this->_makeLink($oxManufacturer->getLink(), $this->_getPageNumber($iPage));
        }
        if ($isSeoActive) {
            $oxViewConfig        = oxNew('oxViewConfig');
            $filterParams        = $oxViewConfig->getAggregationFilter();
            $manufacturerPageUrl = $oxViewConfig->generateSeoUrlFromFilter($manufacturerPageUrl, $filterParams);
        }

        $oxManufacturer->toListLink = $manufacturerPageUrl;
    }

    /**
     * setCategoryPageNumbers
     *
     * @param oxCategory $oCategory
     * @param int        $iPage
     * @param int        $iNrofCatArticles
     * @param int        $iPos
     *
     * @return void
     */
    private function setPrevNextLinks($locatorObject, $iPage, $iNrofCatArticles, $iPos, $searchAddParams = '')
    {
        $sPageNr     = $this->_getPageNumber($iPage);
        $sPageNrPrev = $sPageNrNext = $sPageNr;

        if ($iPos == $iNrofCatArticles) {
            $sPageNrNext = $this->_getPageNumber($iPage + 1);
        }

        if ($iPos == 1) {
            $sPageNrPrev = $this->_getPageNumber($iPage - 1);
        }

        if ($searchAddParams) {
            $sPageNrNext = $sPageNrNext . ($sPageNrNext ? '&amp;' : '') . $searchAddParams;
            $sPageNrPrev = $sPageNrPrev . ($sPageNrPrev ? '&amp;' : '') . $searchAddParams;
        }

        $locatorObject->nextProductLink = $this->_oNextProduct ? $this->_makeLink(
            $this->_oNextProduct->getLink(),
            $sPageNrNext
        ) : null;
        $locatorObject->prevProductLink = $this->_oBackProduct ? $this->_makeLink(
            $this->_oBackProduct->getLink(),
            $sPageNrPrev
        ) : null;
    }

    /**
     * @return bool
     */
    private function useCategoryInheritance()
    {
        if (null !== $this->useCategoryInheritance) {
            return $this->useCategoryInheritance;
        }

        $this->useCategoryInheritance = (bool) oxRegistry::getConfig()->getShopConfVar(
            'makaira_connect_category_inheritance',
            null,
            oxConfig::OXMODULE_MODULE_PREFIX . 'makaira/connect'
        );

        return $this->useCategoryInheritance;
    }

    /**
     * @return string
     */
    private function getSearchAddParams()
    {
        $sSearchParam     = oxRegistry::getConfig()->getRequestParameter('searchparam', true);
        $sSearchLinkParam = rawurlencode($sSearchParam);

        $sSearchCat = oxRegistry::getConfig()->getRequestParameter('searchcnid');
        $sSearchCat = $sSearchCat ? rawurldecode($sSearchCat) : $sSearchCat;

        $sSearchVendor = oxRegistry::getConfig()->getRequestParameter('searchvendor');
        $sSearchVendor = $sSearchVendor ? rawurldecode($sSearchVendor) : $sSearchVendor;

        $sSearchManufacturer = oxRegistry::getConfig()->getRequestParameter('searchmanufacturer');
        $sSearchManufacturer = $sSearchManufacturer ? rawurldecode($sSearchManufacturer) : $sSearchManufacturer;

        $sAddSearch = "searchparam={$sSearchLinkParam}";
        $sAddSearch .= '&amp;listtype=search';

        if ($sSearchCat !== null) {
            $sAddSearch .= "&amp;searchcnid={$sSearchCat}";
        }

        if ($sSearchVendor !== null) {
            $sAddSearch .= "&amp;searchvendor={$sSearchVendor}";
        }

        if ($sSearchManufacturer !== null) {
            $sAddSearch .= "&amp;searchmanufacturer={$sSearchManufacturer}";
        }

        return $sAddSearch;
    }
}
