<?php

class makaira_category_order_ajax extends makaira_category_order_ajax_parent
{
    /**
     * Saves category articles ordering.
     *
     * @return null
     */
    public function saveNewOrder()
    {
        parent::saveNewOrder();

        $this->touchCategoryArticles();
    }

    /**
     * Removes category articles ordering set by saveneworder() method.
     *
     * @return null
     */
    public function remNewOrder()
    {
        parent::remNewOrder();

        $this->touchCategoryArticles();
    }

    /**
     * Removes category articles ordering set by saveneworder() method.
     *
     * @return null
     */
    public function touchCategoryArticles()
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
