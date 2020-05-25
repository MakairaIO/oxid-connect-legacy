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
class makaira_manufacturer_main_ajax extends makaira_manufacturer_main_ajax_parent
{
    public function removeManufacturer()
    {
        $articleIds     = $this->_getActionIds('oxarticles.oxid');
        $manufacturerId = $this->getConfig()->getRequestParameter('oxid');

        if (oxRegistry::getConfig()->getRequestParameter('all')) {
            $container = \Makaira\Connect\Connect::getContainerFactory()->getContainer();
            /** @var \Doctrine\DBAL\Connection $db */
            $db = $container->get(\Doctrine\DBAL\Connection::class);
            $sArtTable = $this->_getViewName('oxarticles');

            $sSelectSql  = "SELECT $sArtTable.`OXID` " . $this->_getQuery();
            $aArticleIds = $db->fetchAll($sSelectSql);
        } else {
            $aArticleIds = array_map(
                function ($item) {
                    return ['OXID' => $item];
                },
                $articleIds
            );
        }

        parent::removeManufacturer();

        if ($aArticleIds) {
            $oArticle = oxNew("oxarticle");
            foreach ($aArticleIds as $aArticleId) {
                $oArticle->touch($aArticleId['OXID']);
            }
        }

        if (oxRegistry::get('makaira_connect_helper')->isOxid6()) {
            $oManufacturer = oxNew("oxmanufacturer");
            $oManufacturer->touch($manufacturerId);
        }
    }

    public function addManufacturer()
    {
        $aAddArticle = $this->_getActionIds('oxarticles.oxid');
        $sSynchOxid  = oxRegistry::getConfig()->getRequestParameter('synchoxid');

        if (oxRegistry::getConfig()->getRequestParameter('all')) {
            $sArtTable   = $this->_getViewName('oxarticles');
            $aAddArticle = $this->_getAll(parent::_addFilter("select $sArtTable.oxid " . $this->_getQuery()));
        }

        parent::addManufacturer();

        if ($aAddArticle) {
            $oArticle = oxNew("oxarticle");
            foreach ($aAddArticle as $aArticleId) {
                $oArticle->touch($aArticleId);
            }
        }

        if (oxRegistry::get('makaira_connect_helper')->isOxid6()) {
            $oManufacturer = oxNew("oxmanufacturer");
            $oManufacturer->touch($sSynchOxid);
        }
    }
}
