<?php
/**
 * This file is part of a marmalade GmbH project
 *
 * It is not Open Source and may not be redistributed.
 * For contact information please visit http://www.marmalade.de
 *
 * Version:    3.4
 * Author URI: http://www.marmalade.de
 */

/**
 * Metadata version
 */
$sMetadataVersion = '1.1';

/**
 * Module information
 */
$aModule = array(
    'id'          => 'makaira/connect',
    'title'       => 'Makaira :: Connect',
    'description' => 'Connector to Makaira',
    'thumbnail'   => 'marmalade.jpg',
    'version'     => '1.0.0',
    'author'      => 'marmalade GmbH',
    'url'         => 'http://www.marmalade.de',
    'email'       => 'support@marmalade.de',
    'extend'      => array(
        'oxarticle'  => 'makaira/connect/src/oxid/application/models/makaira_connect_oxarticle',
        'oxcategory' => 'makaira/connect/src/oxid/application/models/makaira_connect_oxcategory',
    ),
    'files'       => array(
        'makaira_connect_endpoint'       => 'makaira/connect/src/oxid/application/controllers/makaira_connect_endpoint.php',
        'makaira_connect_single_sign_on' => 'makaira/connect/src/oxid/application/controllers/admin/makaira_connect_single_sign_on.php',
        'makaira_connect_events'         => 'makaira/connect/src/oxid/core/makaira_connect_events.php',
    ),
    'templates'   => array(
        'makaira_connect_single_sign_on.tpl' => 'makaira/connect/views/admin/tpl/makaira_connect_single_sign_on.tpl'
    ),
    'blocks'      => array(),
    'events'      => array(
        'onActivate'   => 'makaira_connect_events::onActivate',
        'onDeactivate' => 'makaira_connect_events::onDeactivate',
    ),
    'settings'    => array(
        array('name' => 'makaira_connect_secret', 'group' => 'SETTINGS', 'type' => 'str', 'value' => ''),
        array('name' => 'makaira_application_url', 'group' => 'SETTINGS', 'type' => 'str', 'value' => ''),
        array('name' => 'makaira_connect_load_limit', 'group' => 'SETTINGS', 'type' => 'str', 'value' => ''),
        array('name' => 'makaira_instance', 'group' => 'SETTINGS', 'type' => 'str', 'value' => 'live'),
        array(
            'name'  => 'makaira_field_blacklist_product',
            'group' => 'IMPORTFIELDS',
            'type'  => 'arr',
            'value' => [
                'OXAMITEMID',
                'OXAMTASKID',
                'OXBUNDLEID',
                'OXEXTURL',
                'OXFOLDER',
                'OXNOSTOCKTEXT',
                'OXPIC8',
                'OXPIC9',
                'OXPIC10',
                'OXPIC11',
                'OXPIC12',
                'OXQUESTIONEMAIL',
                'OXREMINDACTIVE',
                'OXREMINDAMOUNT',
                'OXSHOWCUSTOMAGREEMENT',
                'OXSKIPDISCOUNTS',
                'OXSTOCKTEXT',
                'OXSUBCLASS',
                'OXTEMPLATE',
                'OXUPDATEPRICE',
                'OXUPDATEPRICEA',
                'OXUPDATEPRICEB',
                'OXUPDATEPRICEC',
                'OXUPDATEPRICETIME',
                'OXURLDESC',
                'OXURLIMG',
                'OXPIXIEXPORT',
                'OXPIXIEXPORTED',
                'OXORDERINFO',
                'OXVPE',
            ]
        ),
        array(
            'name'  => 'makaira_field_blacklist_category',
            'group' => 'IMPORTFIELDS',
            'type'  => 'arr',
            'value' => [
                'OXVAT',
                'OXSKIPDISCOUNTS',
            ]
        ),
        array(
            'name'  => 'makaira_field_blacklist_manufacturer',
            'group' => 'IMPORTFIELDS',
            'type'  => 'arr',
            'value' => []
        ),
    ),
);
