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
 * Class makaira_connect_oxcategory
 */
class makaira_connect_oxcategory extends makaira_connect_oxcategory_parent
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

    /**
     * @return \Makaira\Connect\Repository
     */
    private function getRepository()
    {
        return oxRegistry::get('yamm_dic')['makaira.connect.repository'];
    }

    public function save()
    {
        $result = parent::save();

        if (!self::$disableMakairaTouch && $result) {
            $this->touch($this->getId());
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
        $result = parent::delete($sOXID);

        if (!self::$disableMakairaTouch && $result) {
            $this->touch($sOXID ?: $this->getId());
        }

        return $result;
    }

    /**
     * @param mixed $oxid
     */
    public function touch($oxid = null)
    {
        $id = $oxid ?: $this->getId();
        $this->getRepository()->touch('category', $id);
    }

    /**
     * @param null|array $aCategoryIds
     * @param bool       $blFlushArticles
     *
     * @return mixed
     */
    public function executeDependencyEvent($aCategoryIds = null, $blFlushArticles = true)
    {
        if (!self::$disableMakairaTouch && null !== $aCategoryIds) {
            foreach ($aCategoryIds as $sCategoryId) {
                $this->touch($sCategoryId);
            }
        }

        return parent::executeDependencyEvent($aCategoryIds, $blFlushArticles);
    }
}
