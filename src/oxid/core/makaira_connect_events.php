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
        self::addUserTokenTable();
        self::migrate();

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
        $sSql = "CREATE TABLE IF NOT EXISTS `makaira_connect_changes` (
            `SEQUENCE` BIGINT NOT NULL AUTO_INCREMENT,
            `TYPE` VARCHAR(32) COLLATE latin1_general_ci NOT NULL,
            `OXID` CHAR(32) COLLATE latin1_general_ci NOT NULL,
            `CHANGED` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX (`OXID`),
            PRIMARY KEY (`SEQUENCE`),
            UNIQUE KEY `uniqueChanges` (`TYPE`, `OXID`)
        ) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;";
        oxDb::getDb()->execute($sSql);
    }

    private static function addUserTokenTable()
    {
        $sSql = "CREATE TABLE IF NOT EXISTS `makaira_connect_usertoken` (
            `USERID` CHAR(32) COLLATE latin1_general_ci NOT NULL,
            `TOKEN` VARCHAR(255),
            `VALID_UNTIL` DATETIME,
            INDEX (`TOKEN`, `VALID_UNTIL`),
            UNIQUE (`TOKEN`),
            PRIMARY KEY (`USERID`)
        ) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;";
        oxDb::getDb()->execute($sSql);
    }

    private static function isMigrationRequired()
    {
        $dbName = oxRegistry::getConfig()->getConfigParam('dbName');
        $keyColumnCount = (int) oxDb::getDb(true)->getOne(
            "SELECT COUNT(*)
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
            WHERE
                CONSTRAINT_SCHEMA = '{$dbName}' AND
                CONSTRAINT_NAME = 'uniqueChanges' AND
                TABLE_NAME = 'makaira_connect_changes'"
        );

        return 0 < $keyColumnCount;
    }

    private static function migrate()
    {
        if (self::isMigrationRequired()) {
            $db = oxDb::getDb(true);

            // Create the migration table
            $db->execute('CREATE TABLE makaira_connect_changes_migrate LIKE makaira_connect_changes');

            // Add unique key constraint
            $db->execute('ALTER TABLE makaira_connect_changes_migrate ADD UNIQUE KEY `uniqueChanges` (`TYPE`, `OXID`)');

            // Copy unique rows
            $db->execute('INSERT INTO makaira_connect_changes_migrate (SEQUENCE, TYPE, OXID, CHANGED)
                SELECT MAX(SEQUENCE), TYPE, OXID, MAX(CHANGED) FROM makaira_connect_changes GROUP BY TYPE, OXID;');

            // Remove old table
            $db->execute('DROP TABLE makaira_connect_changes');

            // Rename migration table
            $db->execute('ALTER TABLE makaira_connect_changes_migrate RENAME TO makaira_connect_changes');
        }
    }
}
