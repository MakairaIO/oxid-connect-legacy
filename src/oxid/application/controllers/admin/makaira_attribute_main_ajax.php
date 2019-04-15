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
 * Class makaira_attribute_main_ajax
 */
class makaira_attribute_main_ajax extends makaira_attribute_main_ajax_parent
{
    public function removeAttrArticle()
    {
        if (oxRegistry::get('makaira_connect_helper')->isOxid6()) {
            $aChosenCat       = $this->_getActionIds('oxobject2attribute.oxid');
            $sO2AttributeView = $this->_getViewName('oxobject2attribute');
            /** @var \Doctrine\DBAL\Connection $db */
            $db = oxRegistry::get('yamm_dic')['doctrine.connection'];

            if (oxRegistry::getConfig()->getRequestParameter('all')) {
                $sSelectSql = "SELECT $sO2AttributeView.`OXOBJECTID` " . $this->_getQuery();
            } else {
                $aChosenCat = array_map(
                    function ($item) use ($db) {
                        return $db->quote($item, 'string');
                    },
                    $aChosenCat
                );
                $sSelectSql =
                    "SELECT `OXOBJECTID` FROM `oxobject2attribute` " .
                    "WHERE `OXID` in (" .
                    implode(", ", $aChosenCat) .
                    ") ";
            }
        }

        parent::removeAttrArticle();

        if (oxRegistry::get('makaira_connect_helper')->isOxid6()) {
            $aArticleIds = $db->fetchAll($sSelectSql);
            if ($aArticleIds) {
                $oArticle = oxNew("oxarticle");
                foreach ($aArticleIds as $aArticleId) {
                    $oArticle->touch($aArticleId['OXOBJECTID']);
                }
            }
        }
    }

    /**
     * @param mixed $articleId
     */
    protected function onArticleAddToAttributeList($articleId)
    {
        $oArticle = oxNew("oxarticle");
        $oArticle->touch($articleId);

        parent::onArticleAddToAttributeList($articleId);
    }
}
