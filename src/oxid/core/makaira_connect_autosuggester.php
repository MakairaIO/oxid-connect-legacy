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
 * Class makaira_connect_autosuggester
 */
class makaira_connect_autosuggester
{
    /**
     * @var oxLang
     */
    private $oxLang;

    public function __construct(oxLang $oxLang)
    {
        $this->oxLang = $oxLang;
    }

    /**
     * Search for search term and build json response
     *
     * @param string $searchPhrase
     *
     * @return string
     */
    public function search($searchPhrase = "")
    {
        $query                     = new Query();
        $query->enableAggregations = false;
        $query->isSearch           = true;
        $query->searchPhrase       = $searchPhrase;
        $query->count              = 7;
        $query->fields             = ['OXID', 'OXTITLE', 'OXVARSELECT'];

        $oxConfig = oxRegistry::getConfig();

        $query->constraints = array_filter(
            [
                Constraints::SHOP      => $oxConfig->getShopId(),
                Constraints::LANGUAGE  => oxRegistry::getLang()->getLanguageAbbr(),
                Constraints::USE_STOCK => $oxConfig->getShopConfVar('blUseStock'),
            ]
        );

        $dic = oxRegistry::get('yamm_dic');
        /** @var SearchHandler $searchHandler */
        $searchHandler = $dic['makaira.connect.searchhandler'];

        $result = $searchHandler->search($query);

        // make result an array
        $aResult = [];

        //        foreach ($suggestions as $suggest) {
        //            $aResult[] = $this->prepareSuggest($suggest);
        //        }
        //
        //        foreach ($oOxSearch->getActiveAdditionalTypes() as $sType) {
        //            $aTypeResults = $oOxSearch->searchAdditionalTypes($query, $sType);
        //            foreach ($aTypeResults as $aTypeResult) {
        //                $aResult[] = $this->addAdditionalSearchresults($aTypeResult, $sType);
        //            }
        //        }

        //Get product results
        foreach ($result->items as $document) {
            $aResult[] = $this->prepareItem($document);
        }

        $aResult = array_filter($aResult);

        return array(
                'count'        => count($aResult),
                'items'        => $aResult,
                'productCount' => $result->total,
        );
    }

    /**
     * Prepare data for additional search types
     *
     * @param array  $aTypeResult
     * @param string $sType
     *
     * @return mixed
     */
    protected function addAdditionalSearchresults($aTypeResult, $sType)
    {
        $sImageLink = '';
        $aItem      = array();

        switch ($sType) {
            case "searchlink":
                $sTitle = $aTypeResult['marm_oxsearch_searchlinks_title'];
                break;
            case "manufacturer":
                $sTitle              = $aTypeResult['oxtitle'];
                $manufacturer        = $this->shopUtils->getManufacturer($aTypeResult['oxid']);
                $sImageLink          = $manufacturer->image;
                $aTypeResult['link'] = ($manufacturer->link) ?: $aTypeResult['link']; // Set manufacturer SEO URL
                break;
            default:
                $sTitle = $aTypeResult['oxtitle'];
        }
        $aItem['label'] = $sTitle;
        $aItem['value'] = $sTitle;
        $aItem['type']  = $sType;
        $aItem['link']  = $aTypeResult['link'];
        if (isset($aTypeResult['external'])) {
            $aItem['external'] = $aTypeResult['external'];
        }
        if ($sImageLink) {
            $aItem['image'] = $sImageLink;
        }
        if (isset($manufacturer) && $manufacturer->thumb) {
            $aItem['thumbnail'] = $manufacturer->thumb;
        }
        $aItem['category'] = $this->shopUtils->translate("MARM_OXSEARCH_CATEGORY_" . strtoupper($sType));

        return $aItem;
    }

    /**
     * Prepare the data based on an oxArticleObject
     *
     * @param object $doc
     *
     * @return array
     */
    protected function prepareItem($doc)
    {
        if (empty($doc->fields['oxtitle'])) {
            return [];
        }

        $product = oxNew('oxarticle');

        if (!$product->load($doc->id)) {
            return [];
        }

        $title = $doc->fields['oxtitle'];
        if (!empty($doc->fields['oxvarselect'])) {
            $title .= ' | ' . $doc->fields['oxvarselect'];
        }
        $aItem['label']     = $title;
        $aItem['value']     = $title;
        $aItem['link']      = $product->getMainLink();
        $aItem['image']     = $product->getIconUrl(1);
        $aItem['thumbnail'] = $product->getThumbnailUrl();
        $aItem['price']     = $this->preparePrice($product->getPrice());
        $aItem['uvp']       = $this->preparePrice($product->getTPrice());
        $aItem['type']      = 'product';
        $aItem['category']  = $this->translate("MAKAIRA_CONNECT_AUTOSUGGEST_CATEGORY_PRODUCTS");

        return $aItem;
    }

    /**
     * Prepare suggest data
     *
     * @param array $suggest
     *
     * @return mixed
     */
    protected function prepareSuggest($suggest)
    {
        $aItem['label']    = $suggest['text'];
        $aItem['value']    = $suggest['text'];
        $aItem['type']     = 'suggest';
        $aItem['link']     = $this->shopUtils->getSearchLink($suggest['text']);
        $aItem['category'] = $this->shopUtils->translate("MARM_OXSEARCH_CATEGORY_SUGGEST");

        return $aItem;
    }

    /**
     * Helper method to format prices for auto-suggest
     *
     * @param $price
     *
     * @return array
     */
    protected function preparePrice($price)
    {
        if (!$price) {
            return array('brutto' => 0, 'netto' => 0);
        } else {
            return array(
                'brutto' => number_format($price->getBruttoPrice(), 2, ',', ''),
                'netto'  => number_format($price->getNettoPrice(), 2, ',', '')
            );
        }
    }

    /**
     * Getter method for shop translations
     *
     * @param string $string
     *
     * @return string
     */
    protected function translate($string)
    {
        return $this->oxLang->translateString($string);
    }
}
