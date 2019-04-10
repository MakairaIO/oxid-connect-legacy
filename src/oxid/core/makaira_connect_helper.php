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
 * Class makaira_connect_init
 */
class makaira_connect_helper
{
    /**
     * @var null|oxConfig
     */
    protected static $_oConfig = null;

    /**
     * @return oxConfig
     */
    protected static function _getConfig()
    {
        if (self::$_oConfig === null) {
            self::$_oConfig = oxRegistry::getConfig();
        }

        return self::$_oConfig;
    }

    /**
     * @return bool
     */
    public static function isOxid6()
    {
        return version_compare('6.0', self::_getConfig()->getVersion(), '<=');
        //return version_compare(self::_getConfig()->getActiveShop()->getFieldData('oxversion'), '6.0', '>=');
    }
}
