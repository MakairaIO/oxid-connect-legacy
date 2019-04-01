<?php

class makaira_category_order_ajax extends makaira_category_order_ajax_parent
{
    /**
     * Removes article from list for sorting in category
     */
    public function removeCatOrderArticle()
    {
        parent::removeCatOrderArticle();
    }

    /**
     * Adds article to list for sorting in category
     */
    public function addCatOrderArticle()
    {
        parent::addCatOrderArticle();
    }

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
            $sSelect = "select oxobjectid from $sO2CView where $sO2CView.oxcatnid=". $db->quote($sId) . ';';
            $oList = $db->fetchAll($sSelect);
            if ($oList) {
                $oArticle = oxNew("oxarticle");
                foreach ($oList as $oObjId) {
                    $oArticle->touch($oObjId['oxobjectid']);
                }
            }
        }
    }
}
