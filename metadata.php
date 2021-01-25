<?php
/**
 * This file is part of a marmalade GmbH project
 * It is not Open Source and may not be redistributed.
 * For contact information please visit http://www.marmalade.de
 * Version:    3.4
 * Author URI: http://www.marmalade.de
 */

/**
 * Metadata version
 */
$sMetadataVersion = '1.1';

$moduleId = 'makaira/connect';

/**
 * Module information
 */
$aModule = [
    'id'          => $moduleId,
    'title'       => 'Makaira :: Connect',
    'description' => [
        'en' => '<p>Connector to Makaira</p><p>The Makaira :: Connect module GDPR is compliant with this version.</p><div class="errorbox"><p>Please deactivate the Makaira :: Tracking module, if available and activated, before you activate the new Connect version.</p></div>',
        'de' => '<p>Connector zum Makaira</p><p>Mit dieser Version ist das Makaira :: Connect Modul DSGVO konform.</p><div class="errorbox"><p>Bitte deaktivieren Sie das Makaira :: Tracking Modul, falls vorhanden und aktiviert, bevor Sie die neue Connect Version aktivieren.</p></div>',
    ],
    'thumbnail'   => 'makaira.jpg',
    'version'     => '2021.1.1',
    'author'      => 'marmalade GmbH',
    'url'         => 'https://www.makaira.io/',
    'email'       => 'support@makaira.io',
    'extend'      => [
        /* controllers */
        'search'                       => "{$moduleId}/src/oxid/application/controllers/makaira_connect_search",
        'alist'                        => "{$moduleId}/src/oxid/application/controllers/makaira_connect_alist",
        'manufacturerlist'             => "{$moduleId}/src/oxid/application/controllers/makaira_connect_manufacturerlist",
        /* admin controllers */
        'article_attribute_ajax'       => "{$moduleId}/src/oxid/application/controllers/admin/makaira_article_attribute_ajax",
        'attribute_main_ajax'          => "{$moduleId}/src/oxid/application/controllers/admin/makaira_attribute_main_ajax",
        'category_order_ajax'          => "{$moduleId}/src/oxid/application/controllers/admin/makaira_category_order_ajax",
        'article_extend_ajax'          => "{$moduleId}/src/oxid/application/controllers/admin/makaira_article_extend_ajax",
        'article_crossselling_ajax'    => "{$moduleId}/src/oxid/application/controllers/admin/makaira_article_crossselling_ajax",
        'manufacturer_main_ajax'       => "{$moduleId}/src/oxid/application/controllers/admin/makaira_manufacturer_main_ajax",
        /* models */
        'oxarticle'                    => "{$moduleId}/src/oxid/application/models/makaira_connect_oxarticle",
        'oxarticlelist'                => "{$moduleId}/src/oxid/application/models/makaira_connect_oxarticlelist",
        'oxcategory'                   => "{$moduleId}/src/oxid/application/models/makaira_connect_oxcategory",
        'oxobject2category'            => "{$moduleId}/src/oxid/application/models/makaira_connect_oxobject2category",
        'oxmanufacturer'               => "{$moduleId}/src/oxid/application/models/makaira_connect_oxmanufacturer",
        /* core */
        'oxviewconfig'                 => "{$moduleId}/src/oxid/core/makaira_connect_oxviewconfig",
        'oxseodecoder'                 => "{$moduleId}/src/oxid/core/makaira_connect_oxseodecoder",
        'oxoutput'                     => "{$moduleId}/src/oxid/core/makaira_connect_oxoutput",
        'oxconfig'                     => "{$moduleId}/src/oxid/core/makaira_connect_oxconfig",
        /* components */
        'oxlocator'                    => "{$moduleId}/src/oxid/application/components/makaira_connect_oxlocator",
    ],
    'files'       => [
        'makaira_connect_helper'          => "{$moduleId}/src/oxid/core/makaira_connect_helper.php",
        'makaira_connect_endpoint'        => "{$moduleId}/src/oxid/application/controllers/makaira_connect_endpoint.php",
        'makaira_connect_single_sign_on'  => "{$moduleId}/src/oxid/application/controllers/admin/makaira_connect_single_sign_on.php",
        'makaira_connect_events'          => "{$moduleId}/src/oxid/core/makaira_connect_events.php",
        'makaira_connect_request_handler' => "{$moduleId}/src/oxid/core/makaira_connect_request_handler.php",
        'makaira_connect_autosuggest'     => "{$moduleId}/src/oxid/application/controllers/makaira_connect_autosuggest.php",
        'makaira_connect_autosuggester'   => "{$moduleId}/src/oxid/core/makaira_connect_autosuggester.php",
        'makaira_connect_econda'          => "{$moduleId}/src/oxid/application/controllers/makaira_connect_econda.php",
        'makaira_tracking_data_generator' => "{$moduleId}/src/oxid/core/makaira_tracking_data_generator.php",
        'makaira_cookie_utils'            => "{$moduleId}/src/oxid/core/makaira_cookie_utils.php"
    ],
    'templates'   => [
        'makaira_connect_single_sign_on.tpl'           => "{$moduleId}/views/admin/tpl/makaira_connect_single_sign_on.tpl",
        /* frontend filter */
        'makaira/filter/base.tpl'                      => "{$moduleId}/views/tpl/filter/base.tpl",
        'makaira/filter/list.tpl'                      => "{$moduleId}/views/tpl/filter/list.tpl",
        'makaira/filter/list_multiselect.tpl'          => "{$moduleId}/views/tpl/filter/list_multiselect.tpl",
        'makaira/filter/range_slider.tpl'              => "{$moduleId}/views/tpl/filter/range_slider.tpl",
        'makaira/filter/range_slider_price.tpl'        => "{$moduleId}/views/tpl/filter/range_slider_price.tpl",
        'makaira/filter/script.tpl'                    => "{$moduleId}/views/tpl/filter/script.tpl",
        'makaira/filter/categorytree.tpl'              => "{$moduleId}/views/tpl/filter/category/tree.tpl",
        /* custom frontend filter */
        'makaira/filter/list_custom_1.tpl'             => "{$moduleId}/views/tpl/filter/custom/list_custom_1.tpl",
        'makaira/filter/list_custom_2.tpl'             => "{$moduleId}/views/tpl/filter/custom/list_custom_2.tpl",
        'makaira/filter/list_multiselect_custom_1.tpl' => "{$moduleId}/views/tpl/filter/custom/list_multiselect_custom_1.tpl",
        'makaira/filter/list_multiselect_custom_2.tpl' => "{$moduleId}/views/tpl/filter/custom/list_multiselect_custom_2.tpl",
        'makaira/filter/range_slider_custom_1.tpl'     => "{$moduleId}/views/tpl/filter/custom/range_slider_custom_1.tpl",
        'makaira/filter/range_slider_custom_2.tpl'     => "{$moduleId}/views/tpl/filter/custom/range_slider_custom_2.tpl",
        /* autosuggest */
        'makaira/autosuggest/assets.tpl'               => "{$moduleId}/views/tpl/autosuggest/assets.tpl",
        'makaira/autosuggest/autosuggest.tpl'          => "{$moduleId}/views/tpl/autosuggest/autosuggest.tpl",
        'makaira/autosuggest/types/products.tpl'       => "{$moduleId}/views/tpl/autosuggest/types/products.tpl",
        'makaira/autosuggest/types/categories.tpl'     => "{$moduleId}/views/tpl/autosuggest/types/categories.tpl",
        'makaira/autosuggest/types/manufacturers.tpl'  => "{$moduleId}/views/tpl/autosuggest/types/manufacturers.tpl",
        'makaira/autosuggest/types/links.tpl'          => "{$moduleId}/views/tpl/autosuggest/types/links.tpl",
        'makaira/autosuggest/types/suggestions.tpl'    => "{$moduleId}/views/tpl/autosuggest/types/suggestions.tpl",
        /* results */
        'makaira/results/search.tpl'                   => "{$moduleId}/views/tpl/results/search.tpl",
        /* econda */
        'makaira/econda_base.tpl'                      => "{$moduleId}/views/tpl/econda_base.tpl",
        'layout/cookie-banner.tpl'                     => "{$moduleId}/views/tpl/layout/cookie-banner.tpl",
    ],
    'blocks'      => [
        [
            'template' => 'layout/base.tpl',
            'block'    => 'base_style',
            'file'     => 'views/blocks/econda.tpl',
        ],
        [
            'template' => 'page/search/search.tpl',
            'block'    => 'search_results',
            'file'     => 'views/blocks/search_results.tpl',
        ],
        [
            'template' => 'page/list/list.tpl',
            'block'    => 'page_list_listbody',
            'file'     => 'views/blocks/page_list_listbody.tpl',
        ],
        [
            'template' => 'widget/header/search.tpl',
            'block'    => 'widget_header_search_form',
            'file'     => 'views/blocks/autosuggest.tpl',
        ],
        [
            'template' => 'layout/header.tpl',
            'block'    => 'layout_header_bottom',
            'file'     => 'views/blocks/layout/header_tpl/layout_header_bottom.tpl',
        ],
    ],
    'events'      => [
        'onActivate'   => 'makaira_connect_events::onActivate',
        'onDeactivate' => 'makaira_connect_events::onDeactivate',
    ],
    'settings'    => [
        ['name' => 'makaira_connect_secret', 'group' => 'SETTINGS', 'type' => 'str', 'value' => ''],
        ['name' => 'makaira_application_url', 'group' => 'SETTINGS', 'type' => 'str', 'value' => ''],
        ['name' => 'makaira_connect_load_limit', 'group' => 'SETTINGS', 'type' => 'str', 'value' => ''],
        ['name' => 'makaira_instance', 'group' => 'SETTINGS', 'type' => 'str', 'value' => 'live'],
        ['name' => 'makaira_connect_activate_search', 'group' => 'SETTINGS', 'type' => 'bool', 'value' => 0],
        ['name' => 'makaira_connect_activate_listing', 'group' => 'SETTINGS', 'type' => 'bool', 'value' => 0],
        ['name' => 'makaira_connect_category_inheritance', 'group' => 'SETTINGS', 'type' => 'bool', 'value' => 0],
        ['name' => 'makaira_connect_categorytree_id', 'group' => 'SETTINGS', 'type' => 'str', 'value' => ''],
        ['name' => 'makaira_connect_seofilter', 'group' => 'SETTINGS', 'type' => 'bool', 'value' => 0],
        ['name' => 'makaira_connect_use_econda', 'group' => 'OPERATIONAL_INTELLIGENCE', 'type' => 'bool', 'value' => 0],
        ['name' => 'makaira_connect_econda_aid', 'group' => 'OPERATIONAL_INTELLIGENCE', 'type' => 'str', 'value' => ''],
        ['name' => 'makaira_connect_econda_cid', 'group' => 'OPERATIONAL_INTELLIGENCE', 'type' => 'str', 'value' => ''],
        ['name' => 'makaira_connect_url_param', 'group' => 'SETTINGS', 'type' => 'str', 'value' => 'makairaFilter'],
        [
            'group' => 'TRACKING_PRIVACY',
            'name'  => 'makaira_tracking_page_id',
            'type'  => 'str',
            'value' => '',
        ],
        [
            'group' => 'TRACKING_PRIVACY',
            'name'  => 'makaira_cookie_banner_enabled',
            'type'  => 'bool',
            'value' => true,
        ],
        ['name'  => 'makaira_connect_use_odoscope',
         'group' => 'OPERATIONAL_INTELLIGENCE',
         'type'  => 'bool',
         'value' => 0,
        ],
        ['name'  => 'makaira_connect_odoscope_siteid',
         'group' => 'OPERATIONAL_INTELLIGENCE',
         'type'  => 'str',
         'value' => '',
        ],
        ['name'  => 'makaira_connect_odoscope_token',
         'group' => 'OPERATIONAL_INTELLIGENCE',
         'type'  => 'str',
         'value' => '',
        ],
        [
            'name'  => 'makaira_field_blacklist_product',
            'group' => 'IMPORTFIELDSANDATTS',
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
            ],
        ],
        [
            'name'  => 'makaira_field_blacklist_category',
            'group' => 'IMPORTFIELDSANDATTS',
            'type'  => 'arr',
            'value' => [
                'OXVAT',
                'OXSKIPDISCOUNTS',
            ],
        ],
        [
            'name'  => 'makaira_field_blacklist_manufacturer',
            'group' => 'IMPORTFIELDSANDATTS',
            'type'  => 'arr',
            'value' => [],
        ],
        [
            'name'  => 'makaira_attribute_as_int',
            'group' => 'IMPORTFIELDSANDATTS',
            'type'  => 'arr',
            'value' => [],
        ],
        [
            'name'  => 'makaira_attribute_as_float',
            'group' => 'IMPORTFIELDSANDATTS',
            'type'  => 'arr',
            'value' => [],
        ],
        [
            'name'  => 'makaira_recommendation_accessories',
            'group' => 'RECOMMENDATION',
            'type'  => 'bool',
            'value' => 0,
        ],
        [
            'name'  => 'makaira_recommendation_accessory_id',
            'group' => 'RECOMMENDATION',
            'type'  => 'str',
        ],
        [
            'name'  => 'makaira_recommendation_cross_selling',
            'group' => 'RECOMMENDATION',
            'type'  => 'bool',
            'value' => 0,
        ],
        [
            'name'  => 'makaira_recommendation_cross_selling_id',
            'group' => 'RECOMMENDATION',
            'type'  => 'str',
        ],
        [
            'name'  => 'makaira_recommendation_similar_products',
            'group' => 'RECOMMENDATION',
            'type'  => 'bool',
            'value' => 0,
        ],
        [
            'name'  => 'makaira_recommendation_similar_products_id',
            'group' => 'RECOMMENDATION',
            'type'  => 'str',
        ],
        [
            'group' => 'makaira_search_results',
            'name'  => 'makaira_search_results_category',
            'type'  => 'str',
            'value' => '-1',
        ],
        [
            'group' => 'makaira_search_results',
            'name'  => 'makaira_search_results_links',
            'type'  => 'str',
            'value' => '-1',
        ],
        [
            'group' => 'makaira_search_results',
            'name'  => 'makaira_search_results_manufacturer',
            'type'  => 'str',
            'value' => '-1',
        ],
        [
            'group' => 'makaira_search_results',
            'name'  => 'makaira_search_results_product',
            'type'  => 'str',
            'value' => '-1',
        ],
        [
            'group' => 'makaira_search_results',
            'name'  => 'makaira_search_results_suggestion',
            'type'  => 'str',
            'value' => '-1',
        ],
    ],
];

if (oxRegistry::getConfig()->getEdition() === 'EE') {
    $aModule['extend']['oxcache'] = 'makaira/connect/src/oxid/core/makaira_connect_oxcache';
}
