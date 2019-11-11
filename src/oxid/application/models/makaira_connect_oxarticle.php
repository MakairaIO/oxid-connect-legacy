<?php
/**
 * This file is part of a marmalade GmbH project
 * It is not Open Source and may not be redistributed.
 * For contact information please visit http://www.marmalade.de
 * Version:    1.0
 * Author:     Thomas Uhlig <uhlig@marmalade.de>
 * Author URI: http://www.marmalade.de
 */

use Makaira\Connect\Exceptions\FeatureNotAvailableException;
use Makaira\Connect\Exception as ConnectException;

/**
 * Class makaira_connect_oxarticle
 */
class makaira_connect_oxarticle extends makaira_connect_oxarticle_parent
{
    /**
     * @var bool
     */
    protected static $disableMakairaTouch = false;

    /**
     * @var bool
     */
    protected static $callParentMethod;

    /**
     * @param        $class
     * @param string $method
     *
     * @return bool
     * @throws \ReflectionException
     */
    protected function hasParentMethod($class, $method = 'executeDependencyEvent')
    {
        if (null === self::$callParentMethod) {
            try {
                self::$callParentMethod = (new ReflectionClass($class))->getParentClass()->hasMethod($method);
            } catch (ReflectionException $e) {
                self::$callParentMethod = false;
            }
        }

        return self::$callParentMethod;
    }

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

    /**
     * @param mixed $sOXID
     *
     * @return mixed
     */
    public function delete($sOXID = null)
    {
        if (!self::$disableMakairaTouch) {
            $this->touch($sOXID);
        }

        return parent::delete($sOXID);
    }

    /**
     * @param mixed $oxid
     *
     * @return mixed
     */
    public function getParentId($oxid = null)
    {
        if (!isset($oxid)) {

            return parent::getParentId();
        } else {
            /** @var \Makaira\Connect\DatabaseInterface $db */
            $db       = oxRegistry::get('yamm_dic')['oxid.database'];
            $parentId = $db->query('SELECT OXPARENTID FROM oxarticles WHERE OXID = :id', ['id' => $oxid]);

            return empty($parentId) ? null : $parentId[0]['OXPARENTID'];
        }
    }

    /**
     * @param mixed $oxid
     */
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
            $db       = oxRegistry::get('yamm_dic')['oxid.database'];
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

        if ($this->hasParentMethod(__CLASS__)) {
            return parent::executeDependencyEvent($iDependencyEvent);
        }
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

        try {
            /** @var oxArticleList|makaira_connect_oxarticlelist $oSimilarlist */
            $oSimilarlist = oxNew('oxarticlelist');
            $oSimilarlist->loadSimilarProducts($this->getId());
        } catch (FeatureNotAvailableException $e) {
            $oxidConfig->saveShopConfVar(
                'bool',
                'makaira_recommendation_similar_products',
                false,
                null,
                oxConfig::OXMODULE_MODULE_PREFIX . 'makaira/connect'
            );

            return parent::getSimilarProducts();
        } catch (ConnectException $e) {
            return parent::getSimilarProducts();
        } catch (Exception $e) {
            return parent::getSimilarProducts();
        }

        return $oSimilarlist;
    }
}
