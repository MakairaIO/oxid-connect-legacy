<?php
/**
 * This file is part of a marmalade GmbH project
 *
 * It is not Open Source and may not be redistributed.
 * For contact information please visit http://www.marmalade.de
 *
 * Version:    1.0
 * Author:     Alexander Kraus <kraus@marmalade.de>
 * Author URI: http://www.marmalade.de
 */

/**
 * The container class for OXIDs module events.
 * We use it to create tables and new columns on activation, and deleting our blocks on deactivation.
 */
class makaira_connect_events
{
    /**
     * Execute action on activate event
     */
    public static function onActivate()
    {
        // Add new table to configurate landing pages
        self::addProductSequenceTable();

        $oDbHandler = oxNew("oxDbMetaDataHandler");
        $oDbHandler->updateViews();
    }

    /**
     * Execute action on deactivate event
     */
    public static function onDeactivate()
    {
    }

    /**
     * Add new table to configurate landing pages
     */
    private static function addProductSequenceTable()
    {
        $sSql = "CREATE TABLE IF NOT EXISTS `makaira_connect_product_changes` (
            `SEQUENCE` BIGINT NOT NULL AUTO_INCREMENT,
            `OXID` CHAR(32) COLLATE latin1_general_ci NOT NULL,
            `CHNAGED` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX (`OXID`),
            PRIMARY KEY (`SEQUENCE`)
        ) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;";
        oxDb::getDb()->execute($sSql);

        $sSql = "INSERT INTO `makaira_connect_product_changes` (`OXID`) SELECT `OXID` FROM oxarticles";
        oxDb::getDb()->execute($sSql);
    }
}
