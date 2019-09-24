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
use Makaira\Connect\Utils\OperationalIntelligence;
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
     * @return array
     */
    public function search($searchPhrase = "")
    {
        $query                     = new Query();
        $query->enableAggregations = false;
        $query->isSearch           = true;
        $query->searchPhrase       = $searchPhrase;
        $query->count              = 7;
        $query->fields             = $this->getFieldsForResults();

        $oxConfig = oxRegistry::getConfig();

        $query->constraints = array_filter(
            [
                Constraints::SHOP      => $oxConfig->getShopId(),
                Constraints::LANGUAGE  => oxRegistry::getLang()->getLanguageAbbr(),
                Constraints::USE_STOCK => $oxConfig->getShopConfVar('blUseStock'),
            ]
        );

        $useEcondaData = oxRegistry::getConfig()->getShopConfVar(
            'makaira_connect_use_econda',
            null,
            oxConfig::OXMODULE_MODULE_PREFIX . 'makaira/connect'
        );
        if ($useEcondaData && isset($_COOKIE['mak_econda_session'])) {
            $econdaData = json_decode($_COOKIE['mak_econda_session']);
            $query->constraints[ Constraints::ECONDA_DATA ] = $econdaData;
        }

        $dic = oxRegistry::get('yamm_dic');

        /** @var OperationalIntelligence $operationalIntelligence */
        $operationalIntelligence = $dic['makaira.connect.operational_intelligence'];
        $operationalIntelligence->apply($query);

        // Hook for request modification
        $this->modifyRequest($query);

        /** @var SearchHandler $searchHandler */
        $searchHandler = $dic['makaira.connect.searchhandler'];
        $debugTrace = $oxConfig->getRequestParameter("mak_debug");

        $result = $searchHandler->search($query, $debugTrace);

        // Hook for result modification
        $this->afterSearchRequest($result);

        // get product results
        $aProducts = [];
        foreach ($result['product']->items as $document) {
            $aProducts[] = $this->loadProductItem($document);
        }
        // filter out empty values
        $aProducts = array_filter($aProducts);

        // get category results
        $aCategories = [];
        if ($result['category']) {
            foreach ($result['category']->items as $document) {
                $aCategories[] = $this->prepareCategoryItem($document);
            }
        }
        // filter out empty values
        $aCategories = array_filter($aCategories);

        // get manufacturer results
        $aManufacturers = [];
        if ($result['manufacturer']) {
            foreach ($result['manufacturer']->items as $document) {
                $aManufacturers[] = $this->prepareManufacturerItem($document);
            }
        }
        // filter out empty values
        $aManufacturers = array_filter($aManufacturers);

        // get searchable links results
        $aLinks = [];
        if ($result['links']) {
            foreach ($result['links']->items as $document) {
                $aLinks[] = $this->prepareLinkItem($document);
            }
        }
        // filter out empty values
        $aLinks = array_filter($aLinks);

        // get suggestion results
        $aSuggestions = [];
        if ($result['suggestion']) {
            foreach ($result['suggestion']->items as $document) {
                $aSuggestions[] = $this->prepareSuggestionItem($document);
            }
        }
        // filter out empty values
        $aSuggestions = array_filter($aSuggestions);

        return [
            'count'         => count($aProducts),
            'products'      => $aProducts,
            'productCount'  => $result['product']->total,
            'categories'    => $aCategories,
            'manufacturers' => $aManufacturers,
            'links'         => $aLinks,
            'suggestions'   => $aSuggestions,
        ];
    }

    /**
     * Prepare the data based on an oxArticleObject
     *
     * @param object $doc
     *
     * @return array
     */
    protected function loadProductItem($doc)
    {
        if (empty($doc->fields['title'])) {
            return [];
        }

        /** @var \oxArticle */
        $product = oxNew('oxarticle');

        if (!$product->load($doc->id)) {
            return [];
        }

        return $this->prepareProductItem($doc, $product);
    }

    protected function prepareCategoryItem($doc)
    {
        if (empty($doc->fields['title'])) {
            return [];
        }

        $category = oxNew('oxcategory');

        if (!$category->load($doc->id)) {
            return [];
        }

        $aItem['label'] = $doc->fields['title'];
        $aItem['link']  = $category->getLink();

        return $aItem;
    }

    protected function prepareManufacturerItem($doc)
    {
        if (empty($doc->fields['title'])) {
            return [];
        }

        $manufacturer = oxNew('oxmanufacturer');

        if (!$manufacturer->load($doc->id)) {
            return [];
        }

        $aItem['label'] = $doc->fields['title'];
        $aItem['link']  = $manufacturer->getLink();
        $aItem['image'] = $manufacturer->getIconUrl();

        return $aItem;
    }

    protected function prepareLinkItem($doc)
    {
        if (empty($doc->fields['title'])) {
            return [];
        }

        $aItem['label'] = $doc->fields['title'];
        $aItem['link']  = $doc->fields['url'];

        return $aItem;
    }

    protected function prepareSuggestionItem($doc)
    {
        if (empty($doc->fields['title'])) {
            return [];
        }

        $aItem['label'] = $doc->fields['title'];

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

    /**
     * Getter method for resulting fields
     *
     * @return array
     */
    protected function getFieldsForResults()
    {
//        $fields = ['OXID', 'OXTITLE', 'OXVARSELECT'];
        $fields = ['id', 'title', 'OXVARSELECT'];

        return $fields;
    }

    /**
     * data preparation hook
     *
     * @param object $doc
     * @param \oxArticle $product
     *
     * @return array
     */
    protected function prepareProductItem(&$doc, &$product)
    {
        $title = $doc->fields['title'];
        if (!empty($doc->fields['oxvarselect'])) {
            $title .= ' | ' . $doc->fields['oxvarselect'];
        }

        $aItem = [];
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
     * @param \Makaira\Query $query
     */
    public function modifyRequest(Query &$query)
    {
    }

    /**
     * @param $result
     */
    public function afterSearchRequest(&$result)
    {
    }
}
