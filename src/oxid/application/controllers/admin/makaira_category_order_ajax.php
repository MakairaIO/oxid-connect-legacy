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
 * Class makaira_category_order_ajax
 */
class makaira_category_order_ajax extends makaira_category_order_ajax_parent
{
    public function saveNewOrder()
    {
        parent::saveNewOrder();

        $this->touchCategoryArticles();
    }

    public function remNewOrder()
    {
        parent::remNewOrder();

        $this->touchCategoryArticles();
    }

    private function touchCategoryArticles()
    {
        $oCategory = oxNew("oxcategory");
        $sId = oxRegistry::getConfig()->getRequestParameter("oxid");
        if ($oCategory->load($sId)) {
            /** @var \Doctrine\DBAL\Connection $db */
            $db = oxRegistry::get('yamm_dic')['doctrine.connection'];

            $sO2CView = $this->_getViewName('oxobject2category');
            $sSelect = "SELECT OXOBJECTID FROM $sO2CView WHERE $sO2CView.OXCATNID=". $db->quote($sId) . ';';
            $oList = $db->fetchAll($sSelect);
            if ($oList) {
                $oArticle = oxNew("oxarticle");
                foreach ($oList as $oObjId) {
                    $oArticle->touch($oObjId['OXOBJECTID']);
                }
            }
        }
    }
}
