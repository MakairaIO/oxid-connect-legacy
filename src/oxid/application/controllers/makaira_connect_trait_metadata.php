<?php

trait makaira_connect_trait_metadata
{
    /**
     * @var string
     */
    private $seoObjectId;

    public function setMakSeoObjectId($seoObjectId)
    {
        $this->seoObjectId = $seoObjectId;
    }

    protected function _getSeoObjectId()
    {
        if ($this->seoObjectId) {
            return $this->seoObjectId;
        }

        return parent::_getSeoObjectId();
    }

    public function resetMetaData()
    {
        $this->_sMetaKeywords    = null;
        $this->_sMetaDescription = null;
        $this->seoObjectId       = null;
    }

    public function makBuildCatTree($category = null)
    {
        if (null == $this->_oCategoryTree) {
            $oCategoryTree = oxNew('oxCategoryList');
            $oCategoryTree->buildTree($category);
            $this->_oCategoryTree = $oCategoryTree;
        }
    }

    public function resetCategoryTree()
    {
        $this->_oCategoryTree = null;
    }
}
