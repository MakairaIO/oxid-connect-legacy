<?php

use Makaira\Connect\RecommendationHandler;
use Makaira\Constraints;
use Makaira\RecommendationQuery;

/**
 * This file is part of a marmalade GmbH project
 * It is not Open Source and may not be redistributed.
 * For contact information please visit http://www.marmalade.de
 *
 * @version 0.1
 * @author  Stefan Krenz <krenz@marmalade.de>
 * @link    http://www.marmalade.de
 */
class makaira_connect_oxarticlelist extends makaira_connect_oxarticlelist_parent
{
    const RECOMMENDATION_TYPE_CROSS_SELLING    = 'cross-selling';
    const RECOMMENDATION_TYPE_ACCESSORIES      = 'accessories';
    const RECOMMENDATION_TYPE_SIMILAR_PRODUCTS = 'similar-products';


    /**
     * @var array
     */
    private static $productCache = [];

    /**
     * @param string $sArticleId
     *
     * @return void
     * @throws \UnexpectedValueException
     * @throws \RuntimeException
     * @throws oxSystemComponentException
     */
    public function loadArticleAccessoires($sArticleId)
    {
        $oxidConfig = oxRegistry::getConfig();

        $accessoryEnabled = $oxidConfig->getShopConfVar(
            'makaira_recommendation_accessories',
            null,
            oxConfig::OXMODULE_MODULE_PREFIX . 'makaira/connect'
        );

        if (!$accessoryEnabled) {
            parent::loadArticleAccessoires($sArticleId);

            return;
        }

        $recommendationId = $oxidConfig->getShopConfVar(
            'makaira_recommendation_accessory_id',
            null,
            oxConfig::OXMODULE_MODULE_PREFIX . 'makaira/connect'
        );

        $this->fetchFromMakaira(
            self::RECOMMENDATION_TYPE_ACCESSORIES,
            $recommendationId,
            $sArticleId,
            $oxidConfig->getConfigParam('iNrofCrossellArticles')
        );
    }

    /**
     * @param string $recommendationType
     * @param string $recommendationId
     * @param string $productId
     * @param int    $count
     *
     * @throws \UnexpectedValueException
     * @throws \RuntimeException
     * @throws oxSystemComponentException
     */
    protected function fetchFromMakaira($recommendationType, $recommendationId, $productId, $count = 50)
    {
        $dic = oxRegistry::get('yamm_dic');
        /** @var RecommendationHandler $handler */
        $handler = $dic['makaira.connect.recommendationhandler'];

        $query                   = new RecommendationQuery();
        $query->recommendationId = $recommendationId;
        $query->productId        = $productId;
        $query->requestId        = hash('sha256', microtime(true));
        $query->count            = $count;

        $product = $this->getProduct($productId);
        if ($category = $product->getCategory()) {
            $query->categoryId = $category->getId();
        } elseif ($categoryId = oxRegistry::get('oxviewconfig')->getActCatId()) {
            $query->categoryId = $categoryId;
        }

        // Hook to define custom price ranges.
        $priceRange   = $this->getPriceRange($recommendationType, $product);
        $productPrice = $product->getPrice()->getPrice();

        if (array_key_exists('min', $priceRange) && $priceRange['min']) {
            $query->priceRangeMin = $productPrice * (float) $priceRange['min'];
        }

        if (array_key_exists('max', $priceRange) && $priceRange['max']) {
            $query->priceRangeMax = $productPrice * (float) $priceRange['max'];
        }

        $query->constraints[Constraints::SHOP]      = oxRegistry::getConfig()->getShopId();
        $query->constraints[Constraints::LANGUAGE]  = oxRegistry::getLang()->getLanguageAbbr();
        $query->constraints[Constraints::USE_STOCK] = oxRegistry::getConfig()->getShopConfVar('blUseStock');

        $result = $handler->recommendation($query);

        // Use this snippet if you want to use the Makaira response directly.
        /*
        $products = array_map(
            function ($item) {
                return $item->fields;
            },
            $result->items
        );
        $this->assignArray($products);
        */

        $productIds = array_map(
            function ($item) {
                return $item->id;
            },
            $result->items
        );
        $this->loadIds($productIds);
        $this->sortByIds($productIds);
    }

    /**
     * @param string $productId
     *
     * @return oxArticle
     * @throws oxSystemComponentException
     */
    protected function getProduct($productId)
    {
        if (!array_key_exists($productId, self::$productCache)) {
            self::$productCache[$productId] = oxNew('oxarticle');
            self::$productCache[$productId]->load($productId);
        }

        return self::$productCache[$productId];
    }

    /**
     * Hook to set
     *
     * @param string    $type    Recommendation type (e.g. cross-selling, accessories)
     * @param oxArticle $product The product instance
     *
     * @return array
     */
    protected function getPriceRange($type, $product)
    {
        $priceRange = [];

        if (self::RECOMMENDATION_TYPE_ACCESSORIES === $type) {
            $priceRange = [
                'min' => 0.7,
                'max' => 1.9,
            ];
        }

        if (self::RECOMMENDATION_TYPE_CROSS_SELLING === $type) {
            $priceRange = [
                'min' => 0.9,
            ];
        }

        if (self::RECOMMENDATION_TYPE_SIMILAR_PRODUCTS === $type) {
            $priceRange = [
                'min' => 0.8,
                'max' => 1.6,
            ];
        }

        return $priceRange;
    }

    /**
     * @param string $sArticleId
     *
     * @return void
     * @throws \UnexpectedValueException
     * @throws \RuntimeException
     * @throws oxSystemComponentException
     */
    public function loadArticleCrossSell($sArticleId)
    {
        $oxidConfig = oxRegistry::getConfig();

        $crosssellingEnabled = $oxidConfig->getShopConfVar(
            'makaira_recommendation_cross_selling',
            null,
            oxConfig::OXMODULE_MODULE_PREFIX . 'makaira/connect'
        );

        if (!$crosssellingEnabled) {
            parent::loadArticleCrossSell($sArticleId);

            return;
        }

        $recommendationId = $oxidConfig->getShopConfVar(
            'makaira_recommendation_cross_selling_id',
            null,
            oxConfig::OXMODULE_MODULE_PREFIX . 'makaira/connect'
        );

        $this->fetchFromMakaira(
            self::RECOMMENDATION_TYPE_CROSS_SELLING,
            $recommendationId,
            $sArticleId,
            $oxidConfig->getConfigParam('iNrofCrossellArticles')
        );
    }

    /**
     * @param $sArticleId
     *
     * @throws oxSystemComponentException
     */
    public function loadSimilarProducts($sArticleId)
    {
        $oxidConfig = oxRegistry::getConfig();

        $recommendationId = $oxidConfig->getShopConfVar(
            'makaira_recommendation_similar_products_id',
            null,
            oxConfig::OXMODULE_MODULE_PREFIX . 'makaira/connect'
        );

        $this->fetchFromMakaira(
            self::RECOMMENDATION_TYPE_SIMILAR_PRODUCTS,
            $recommendationId,
            $sArticleId,
            $oxidConfig->getConfigParam('iNrofSimilarArticles')
        );
    }
}
