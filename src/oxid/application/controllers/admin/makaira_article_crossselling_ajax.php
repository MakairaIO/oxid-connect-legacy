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
 * Class makaira_article_crossselling_ajax
 */
class makaira_article_crossselling_ajax extends makaira_article_crossselling_ajax_parent
{
    public function removeArticleCross()
    {
        $aChosenArt = $this->_getActionIds('oxobject2article.oxid');
        $sO2ArticleView = $this->_getViewName('oxobject2article');
        /** @var \Doctrine\DBAL\Connection $db */
        $db = oxRegistry::get('yamm_dic')['doctrine.connection'];

        if (oxRegistry::getConfig()->getRequestParameter('all')) {
            $sSelectSql = "SELECT $sO2ArticleView.`OXOBJECTID` " . $this->_getQuery();
        } else {
            $aChosenArt = array_map(function ($item) use ($db) {
                return $db->quote($item, 'string');
            }, $aChosenArt);
            $sSelectSql = "SELECT `OXOBJECTID` FROM `oxobject2article` " .
                "WHERE `OXID` in (" . implode(", ", $aChosenArt) . ") ";
        }
        $aArticleIds = $db->fetchAll($sSelectSql);

        parent::removeArticleCross();

        if ($aArticleIds) {
            $oArticle = oxNew("oxarticle");
            foreach ($aArticleIds as $aArticleId) {
                $oArticle->touch($aArticleId['OXOBJECTID']);
            }
        }
    }

    public function addArticleCross()
    {
        $aChosenArt = $this->_getActionIds('oxarticles.oxid');

        if (oxRegistry::getConfig()->getRequestParameter('all')) {
            $sArtTable  = $this->_getViewName('oxarticles');
            $aChosenArt = $this->_getAll(parent::_addFilter("select $sArtTable.oxid " . $this->_getQuery()));
        }

        parent::addArticleCross();

        if ($aChosenArt) {
            $oArticle = oxNew("oxarticle");
            foreach ($aChosenArt as $aArticleId) {
                $oArticle->touch($aArticleId);
            }
        }
    }

    /**
     * @param oxarticle $article
     */
    protected function onArticleAddingToCrossSelling($article)
    {
        $article->touch();

        parent::onArticleAddingToCrossSelling($article);
    }
}
