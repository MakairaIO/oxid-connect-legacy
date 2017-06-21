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
        /* controllers */
        'search' => 'makaira/connect/src/oxid/application/controllers/makaira_connect_search',
        'alist' => 'makaira/connect/src/oxid/application/controllers/makaira_connect_alist',
        /* models */
        'oxarticle'  => 'makaira/connect/src/oxid/application/models/makaira_connect_oxarticle',
        'oxcategory' => 'makaira/connect/src/oxid/application/models/makaira_connect_oxcategory',
    ),
    'files'       => array(
        'makaira_connect_endpoint'        => 'makaira/connect/src/oxid/application/controllers/makaira_connect_endpoint.php',
        'makaira_connect_single_sign_on'  => 'makaira/connect/src/oxid/application/controllers/admin/makaira_connect_single_sign_on.php',
        'makaira_connect_events'          => 'makaira/connect/src/oxid/core/makaira_connect_events.php',
        'makaira_connect_request_handler' => 'makaira/connect/src/oxid/core/makaira_connect_request_handler.php',
    ),
    'templates'   => array(
        'makaira_connect_single_sign_on.tpl' => 'makaira/connect/views/admin/tpl/makaira_connect_single_sign_on.tpl',
        /* frontend filter */
        'makaira/filter/base.tpl' => 'makaira/connect/views/tpl/filter/base.tpl',
        'makaira/filter/list_multiselect.tpl' => 'makaira/connect/views/tpl/filter/list_multiselect.tpl',
        'makaira/filter/range_slider.tpl' => 'makaira/connect/views/tpl/filter/range_slider.tpl',
        'makaira/filter/list_show_more.tpl' => 'makaira/connect/views/tpl/filter/list_show_more.tpl',
    ),
    'blocks'      => array(
        array(
            'template' => 'layout/sidebar.tpl',
            'block'    => 'sidebar_categoriestree',
            'file'     => 'views/blocks/sidebar_categoriestree.tpl',
        ),
    ),
    'events'      => array(
        'onActivate'   => 'makaira_connect_events::onActivate',
        'onDeactivate' => 'makaira_connect_events::onDeactivate',
    ),
    'settings'    => array(
        array('name' => 'makaira_connect_secret', 'group' => 'SETTINGS', 'type' => 'str', 'value' => ''),
        array('name' => 'makaira_application_url', 'group' => 'SETTINGS', 'type' => 'str', 'value' => ''),
        array('name' => 'makaira_connect_load_limit', 'group' => 'SETTINGS', 'type' => 'str', 'value' => ''),
        array('name' => 'makaira_instance', 'group' => 'SETTINGS', 'type' => 'str', 'value' => 'live'),
        array('name' => 'makaira_connect_activate_search', 'group' => 'SETTINGS', 'type' => 'bool', 'value' => 0),
        array('name' => 'makaira_connect_activate_listing', 'group' => 'SETTINGS', 'type' => 'bool', 'value' => 0),
    ),
);
