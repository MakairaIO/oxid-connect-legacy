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
    'version'     => '2017-dev',
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
        /* core */
        'oxviewconfig' => 'makaira/connect/src/oxid/core/makaira_connect_oxviewconfig',
    ),
    'files'       => array(
        'makaira_connect_endpoint'        => 'makaira/connect/src/oxid/application/controllers/makaira_connect_endpoint.php',
        'makaira_connect_single_sign_on'  => 'makaira/connect/src/oxid/application/controllers/admin/makaira_connect_single_sign_on.php',
        'makaira_connect_events'          => 'makaira/connect/src/oxid/core/makaira_connect_events.php',
        'makaira_connect_request_handler' => 'makaira/connect/src/oxid/core/makaira_connect_request_handler.php',
        'makaira_connect_autosuggest'     => 'makaira/connect/src/oxid/application/controllers/makaira_connect_autosuggest.php',
        'makaira_autosuggester'     => 'makaira/connect/Lib/makaira_autosuggester.php',
        'makaira_autosuggester_adapter'     => 'makaira/connect/Lib/makaira_autosuggester_adapter.php',
        'oxid_autosuggester'     => 'makaira/connect/Adapter/oxid_autosuggester.php',
    ),
    'templates'   => array(
        'makaira_connect_single_sign_on.tpl' => 'makaira/connect/views/admin/tpl/makaira_connect_single_sign_on.tpl',
        /* frontend filter */
        'makaira/filter/base.tpl' => 'makaira/connect/views/tpl/filter/base.tpl',
        'makaira/filter/list.tpl' => 'makaira/connect/views/tpl/filter/list.tpl',
        'makaira/filter/list_multiselect.tpl' => 'makaira/connect/views/tpl/filter/list_multiselect.tpl',
        'makaira/filter/range_slider.tpl' => 'makaira/connect/views/tpl/filter/range_slider.tpl',
        'makaira/filter/script.tpl' => 'makaira/connect/views/tpl/filter/script.tpl',
        /* autosuggest */
        'makaira/autosuggest/autosuggest.tpl' => 'makaira/connect/views/tpl/autosuggest/autosuggest.tpl',
        'makaira/autosuggest/types/products.tpl' => 'makaira/connect/views/tpl/autosuggest/types/products.tpl',
        'makaira/autosuggest/types/categories.tpl' => 'makaira/connect/views/tpl/autosuggest/types/categories.tpl',
        'makaira/autosuggest/types/manufacturers.tpl' => 'makaira/connect/views/tpl/autosuggest/types/manufacturers.tpl',
        'makaira/autosuggest/types/links.tpl' => 'makaira/connect/views/tpl/autosuggest/types/links.tpl',
        /* results */
        'makaira/results/search.tpl' => 'makaira/connect/views/tpl/results/search.tpl',
    ),
    'blocks'      => array(
        array(
            'template' => 'page/search/search.tpl',
            'block'    => 'search_results',
            'file'     => 'views/blocks/search_results.tpl',
        ),
        array(
            'template' => 'page/list/list.tpl',
            'block' => 'page_list_listbody',
            'file'     => 'views/blocks/page_list_listbody.tpl',
        ),
        array(
            'template' => 'widget/header/search.tpl',
            'block'    => 'widget_header_search_form',
            'file'     => 'views/blocks/autosuggest.tpl',
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
        array('name' => 'makaira_connect_category_inheritance', 'group' => 'SETTINGS', 'type' => 'bool', 'value' => 0),
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
