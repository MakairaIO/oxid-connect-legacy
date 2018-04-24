<?php
/**
 * This file is part of a marmalade GmbH project
 * It is not Open Source and may not be redistributed.
 * For contact information please visit http://www.marmalade.de
 * Version:    1.0
 * Author:     Jens Richter <richter@marmalade.de>
 * Author URI: http://www.marmalade.de
 */

class makaira_connect_oxobject2category extends makaira_connect_oxobject2category_parent
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
            $oxArticle = oxNew('oxarticle');
            $oxArticle->touch($this->oxobject2category__oxobjectid->value);
        }
        return $result;
    }
}
