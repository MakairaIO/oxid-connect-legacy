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
 * Class makaira_article_attribute_ajax
 */
class makaira_article_attribute_ajax extends makaira_article_attribute_ajax_parent
{
    /**
     * @param mixed $articleId
     */
    protected function onArticleAttributeRelationChange($articleId)
    {
        $oArticle = oxNew("oxarticle");
        $oArticle->touch($articleId);

        parent::onArticleAttributeRelationChange($articleId);
    }

    /**
     * @param oxarticle $article
     */
    protected function onAttributeValueChange($article)
    {
        $article->touch();

        parent::onAttributeValueChange($article);
    }
}
