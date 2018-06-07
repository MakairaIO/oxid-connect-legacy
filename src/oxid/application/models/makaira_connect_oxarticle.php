<?php

class makaira_connect_oxarticle extends makaira_connect_oxarticle_parent
{
    /**
     * @var bool
     */
    protected static $disableMakairaTouch = false;

    /**
     * @param bool $disableTouch
     */
    public function disableMakairaTouch($disableTouch = true)
    {
        self::$disableMakairaTouch = $disableTouch;
    }

    public function save()
    {
        $result = parent::save();
        if (!self::$disableMakairaTouch && $result) {
            $this->touch();
        }
        return $result;
    }

    public function delete($sOXID = null)
    {
        if (!self::$disableMakairaTouch) {
            $this->touch($sOXID);
        }
        return parent::delete($sOXID);
    }

    public function getParentId($oxid = null)
    {
        if (!isset($oxid)) {
            return parent::getParentId();
        } else {
            /** @var \Makaira\Connect\DatabaseInterface $db */
            $db = oxRegistry::get('yamm_dic')['oxid.database'];
            $parentId = $db->query('SELECT OXPARENTID FROM oxarticles WHERE OXID = :id', ['id' => $oxid]);
            return empty($parentId) ? null : $parentId[0]['OXPARENTID'];
        }
    }

    public function touch($oxid = null)
    {
        $id = $oxid ?: $this->getId();
        if (!$id) {
            return;
        }
        if ($parentId = $this->getParentId($oxid)) {
            $this->getRepository()->touch('product', $parentId);
            $this->getRepository()->touch('variant', $id);
        } else {
            $this->getRepository()->touch('product', $id);
            /** @var \Makaira\Connect\DatabaseInterface $db */
            $db = oxRegistry::get('yamm_dic')['oxid.database'];
            $variants = $db->query(
                'SELECT OXID FROM oxarticles WHERE OXPARENTID = :parentId',
                ['parentId' => $id]
            );
            foreach ($variants as $variant) {
                $this->getRepository()->touch('variant', $variant['OXID']);
            }
        }
    }

    public function updateSoldAmount($dAmount = 0)
    {
        $result = parent::updateSoldAmount($dAmount);
        if (!$dAmount) {
            return $result;
        }
        if (!self::$disableMakairaTouch) {
            $this->touch();
        }

        return $result;
    }

    /**
     * @return \Makaira\Connect\Repository
     */
    private function getRepository()
    {
        return oxRegistry::get('yamm_dic')['makaira.connect.repository'];
    }

    public function executeDependencyEvent($iDependencyEvent = null)
    {
        if (!self::$disableMakairaTouch) {
            $this->touch($this->getId());
        }
        return parent::executeDependencyEvent($iDependencyEvent);
    }

    /**
     * Returns a list of similar products.
     *
     * @return oxArticleList
     * @throws oxSystemComponentException
     */
    public function getSimilarProducts()
    {
        $oxidConfig = oxRegistry::getConfig();

        if (!$oxidConfig->getConfigParam('bl_perfLoadSimilar')) {
            return null;
        }

        $similarProductsEnabled = $oxidConfig->getShopConfVar(
            'makaira_recommendation_similar_products',
            null,
            oxConfig::OXMODULE_MODULE_PREFIX . 'makaira/connect'
        );

        if (!$similarProductsEnabled) {
            return parent::getSimilarProducts();
        }

        /** @var oxArticleList|makaira_connect_oxarticlelist $oSimilarlist */
        $oSimilarlist = oxNew('oxarticlelist');
        $oSimilarlist->loadSimilarProducts($this->getId());

        return $oSimilarlist;
    }
}
