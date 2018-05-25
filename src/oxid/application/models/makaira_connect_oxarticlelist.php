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
    public function loadArticleAccessoires($sArticleId)
    {
        $oxidConfig = oxRegistry::getConfig();

        if (
        !$oxidConfig->getShopConfVar(
            'makaira_recommendation_accessories',
            null,
            oxConfig::OXMODULE_MODULE_PREFIX . 'makaira/connect'
        )
        ) {
            parent::loadArticleAccessoires($sArticleId);

            return;
        }

        $recommendationId = $oxidConfig->getShopConfVar(
            'makaira_recommendation_accessory_id',
            null,
            oxConfig::OXMODULE_MODULE_PREFIX . 'makaira/connect'
        );

        $this->fetchFromMakaira($recommendationId, $sArticleId, $oxidConfig->getConfigParam('iNrofCrossellArticles'));
    }

    protected function fetchFromMakaira($recommendationId, $productId, $count = 50)
    {
        $dic = oxRegistry::get('yamm_dic');
        /** @var RecommendationHandler $handler */
        $handler = $dic['makaira.connect.recommendationhandler'];

        $query                   = new RecommendationQuery();
        $query->recommendationId = $recommendationId;
        $query->productId        = $productId;
        $query->requestId        = hash('sha256', microtime(true));
        $query->count            = $count;

        $query->constraints[Constraints::SHOP]     = oxRegistry::getConfig()
            ->getShopId();
        $query->constraints[Constraints::LANGUAGE] = oxRegistry::getLang()
            ->getLanguageAbbr();

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

    public function loadArticleCrossSell($sArticleId)
    {
        $oxidConfig = oxRegistry::getConfig();

        if (
        !$oxidConfig->getShopConfVar(
            'makaira_recommendation_cross_selling',
            null,
            oxConfig::OXMODULE_MODULE_PREFIX . 'makaira/connect'
        )
        ) {
            parent::loadArticleCrossSell($sArticleId);

            return;
        }

        $recommendationId = $oxidConfig->getShopConfVar(
            'makaira_recommendation_cross_selling_id',
            null,
            oxConfig::OXMODULE_MODULE_PREFIX . 'makaira/connect'
        );

        $this->fetchFromMakaira($recommendationId, $sArticleId, $oxidConfig->getConfigParam('iNrofCrossellArticles'));
    }
}
