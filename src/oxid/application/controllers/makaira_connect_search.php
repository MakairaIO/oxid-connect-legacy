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
use Makaira\Connect\Exception as ConnectException;

/**
 * Class makaira_connect_search
 */
class makaira_connect_search extends makaira_connect_search_parent
{
    protected $aggregations = null;
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
        try {
            // do not use parent::init() to prevent database query
            // oxubase::init() has to be called statically, otherwise essential smarty _tpl_vars are not available
            oxubase::init();
            // check for filter reset function call
            if ('resetmakairafilter' === strtolower($this->getFncName())) {
                $this->resetMakairaFilter();
            }

            if ('redirectmakairafilter' === strtolower($this->getFncName())) {
                $this->redirectMakairaFilter();
            }

            $this->makairaInitSearch();
            $this->addTplParam('isMakairaSearchEnabled', true);
        } catch (ConnectException $e) {
            $oxException = new oxException($e->getMessage(), $e->getCode());
            $oxException->debugOut();
            parent::init();
        } catch (Exception $e) {
            $oxException = new oxException($e->getMessage(), $e->getCode());
            $oxException->debugOut();
            parent::init();
        }
        stopProfile('searchView');
    }

    public function redirectMakairaFilter()
    {
        $this->getViewConfig()->redirectMakairaFilter($this->getLink(), true);
    }

    public function getSortIdent()
    {
        return 'search';
    }

    public function getAggregations()
    {
        return $this->aggregations;
    }

    public function getAddUrlParams()
    {
        $this->getViewConfig()->savePageNumberToCookie();

        return parent::getAddUrlParams();
    }

    public function resetMakairaFilter()
    {
        $this->getViewConfig()->resetMakairaFilter('search', $this->getViewConfig()->getActSearchParam());
    }

    protected function makairaInitSearch()
    {
        $oxConfig = $this->getConfig();

        $query = new Query([
            'searchPhrase' => $oxConfig->getRequestParameter('searchparam', true),
            'isSearch' => true,
        ]);

        /** @var makaira_connect_request_handler $requestHelper */
        $requestHelper = oxNew('makaira_connect_request_handler');

        $query->constraints = array_filter(
            [
                Constraints::SHOP         => $oxConfig->getShopId(),
                Constraints::LANGUAGE     => oxRegistry::getLang()->getLanguageAbbr(),
                Constraints::USE_STOCK    => $oxConfig->getShopConfVar('blUseStock'),
                Constraints::CATEGORY     => rawurldecode($oxConfig->getRequestParameter('searchcnid')),
                Constraints::MANUFACTURER => rawurldecode($oxConfig->getRequestParameter('searchmanufacturer')),
                Constraints::VENDOR       => rawurldecode($oxConfig->getRequestParameter('searchvendor')),
            ]
        );
        $query->aggregations = array_filter(
            $this->getViewConfig()->getAggregationFilter(),
            function ($value) {
                return (bool) $value || ("0" === $value);
            }
        );
        $sorting = $this->getSorting($this->getSortIdent());
        $query->sorting = $requestHelper->sanitizeSorting($sorting);

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

        $oSearchList             = $requestHelper->getProductsFromMakaira($query);
        $this->aggregations      = $requestHelper->getAggregations();
        $this->additionalResults = $requestHelper->getAdditionalResults();

        $this->checkForSearchRedirect();

        $isPaginationActive = $oxConfig->getRequestParameter('pgNr') !== null;
        if (empty($query->aggregations) && 1 === $oSearchList->count() && !$isPaginationActive) {
            $productNumberProduct = $oSearchList->current();
            oxRegistry::getUtils()->redirect($productNumberProduct->getLink(), false, 302);
        }

        // list of found articles
        $this->_aArticleList = $oSearchList;
        $this->_iAllArtCnt = 0;

        // skip count calculation if no articles in list found
        if ($oSearchList->count()) {
            $this->_iAllArtCnt = $requestHelper->getProductCount($query);
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

        $this->_aViewData = $this->modifyViewData($this->_aViewData, $requestHelper);
    }

    protected function modifyViewData($_aViewData, $requestHelper)
    {
        return $_aViewData;
    }

    protected function isSearchEmpty(Query $query)
    {
        $isEmptySearch = empty($query->searchPhrase) && empty($query->aggregations);

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

    private function checkForSearchRedirect()
    {
        $additionalResults = (array) $this->additionalResults;
        $redirects         =
            isset($additionalResults['searchredirect']) ? $additionalResults['searchredirect']->items : [];

        if (count($redirects) > 0) {
            $targetUrl = $redirects[0]->fields['targetUrl'];

            if ($targetUrl) {
                oxRegistry::getUtils()
                    ->redirect($targetUrl, false, 302);
            }
        }
    }
}
