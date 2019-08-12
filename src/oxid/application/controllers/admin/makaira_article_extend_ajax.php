<?php
/**
 * This file is part of a marmalade GmbH project
 * It is not Open Source and may not be redistributed.
 * For contact information please visit http://www.marmalade.de
 * Version:    1.0
 * Author:     Thomas Uhlig <uhlig@marmalade.de>
 * Author URI: http://www.marmalade.de
 */

/**
 * Class makaira_manufacturer_main_ajax
 */
class makaira_article_extend_ajax extends makaira_article_extend_ajax_parent
{
    /**
     * @param array  $categoriesToRemove
     * @param string $oxId
     */
    public function onCategoriesRemoval($categoriesToRemove, $oxId)
    {
        parent::onCategoriesRemoval($categoriesToRemove, $oxId);

        $oCategory = oxNew('oxcategory');
        $oCategory->executeDependencyEvent($categoriesToRemove);

        $oArticle = oxNew('oxarticle');
        $oArticle->touch($oxId);
    }

    /**
     * Adds article to chosen category
     */
    public function addCat()
    {
        parent::addCat();

        $oArticle = oxNew('oxarticle');
        $oxId     = oxRegistry::getConfig()->getRequestParameter('synchoxid');
        $oArticle->touch($oxId);
    }

    /**
     * @param array $categories
     */
    protected function onCategoriesAdd($categories)
    {
        parent::onCategoriesAdd($categories);

        $oCategory = oxNew('oxcategory');
        $oCategory->executeDependencyEvent($categories);
    }

    /**
     * Sets selected category as a default
     */
    public function setAsDefault()
    {
        parent::setAsDefault();

        $oArticle = oxNew('oxarticle');
        $oxId     = oxRegistry::getConfig()->getRequestParameter('oxid');
        $oArticle->touch($oxId);
    }
}
