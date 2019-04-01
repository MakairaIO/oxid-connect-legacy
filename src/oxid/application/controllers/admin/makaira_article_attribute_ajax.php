<?php

class makaira_article_attribute_ajax extends makaira_article_attribute_ajax_parent
{
    /**
     * Removes article attributes.
     */
    public function removeAttr()
    {
        parent::removeAttr();
    }

    /**
     * Adds attributes to article.
     */
    public function addAttr()
    {
        parent::addAttr();
    }

    /**
     * Saves attribute value
     */
    public function saveAttributeValue()
    {
        parent::saveAttributeValue();
    }

    /**
     * Method is used to bind to attribute and article relation change action.
     *
     * @param string $articleId
     */
    protected function onArticleAttributeRelationChange($articleId)
    {
        $oArticle = oxNew("oxarticle");
        $oArticle->touch($articleId);

        parent::onArticleAttributeRelationChange($articleId);
    }

    /**
     * Method is used to bind to attribute value change.
     *
     * @param \OxidEsales\Eshop\Application\Model\Article $article
     */
    protected function onAttributeValueChange($article)
    {
        $article->touch();

        parent::onAttributeValueChange($article);
    }
}
