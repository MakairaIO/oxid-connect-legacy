<?php

require_once __DIR__ . '/vendor/autoload.php';

/** @var \Marm\Yamm\DIC $dic */

$dic['oxid.database'] = function (\Marm\Yamm\DIC $dic) {
    return new \Makaira\Connect\Database\OxidDatabase(
        oxDb::getInstance()->getDb(), $dic['oxid.table_translator']
    );
};

$dic['content_parsers.oxid.smarty'] = function (\Marm\Yamm\DIC $dic) {
    return new \Makaira\Connect\Utils\OxidSmartyParser(
        oxRegistry::getLang(), oxRegistry::get('oxutilsview')
    );
};

$dic['makaira.content_parser'] = function (\Marm\Yamm\DIC $dic) {
    return $dic['content_parsers.oxid.smarty'];
};

$dic['oxid.table_translator'] = function (\Marm\Yamm\DIC $dic) {
    return new \Makaira\Connect\Utils\TableTranslator(
        [
            'oxarticles',
            'oxartextends',
            'oxattribute',
            'oxcategories',
            'oxmanufacturers',
            'oxobject2attribute',
        ]
    );
};

// --------------------------------------

$dic['makaira.connect.repository'] = function (\Marm\Yamm\DIC $dic) {
    return new Makaira\Connect\Repository(
        $dic['oxid.database'], [
            'product'  => $dic['makaira.connect.repository.product'],
            'variant'  => $dic['makaira.connect.repository.variant'],
            'category' => $dic['makaira.connect.repository.category'],
        ]
    );
};

$dic['makaira.connect.repository.product'] = function (\Marm\Yamm\DIC $dic) {
    return new Makaira\Connect\Repository\ProductRepository(
        $dic['oxid.database'], new Makaira\Connect\Repository\ModifierList(
            $dic->getTagged('makaira.importer.modifier.product')
        )
    );
};

$dic['makaira.connect.repository.variant'] = function (\Marm\Yamm\DIC $dic) {
    return new Makaira\Connect\Repository\VariantRepository(
        $dic['oxid.database'], new Makaira\Connect\Repository\ModifierList(
            $dic->getTagged('makaira.importer.modifier.variant')
        )
    );
};

$dic['makaira.connect.repository.category'] = function (\Marm\Yamm\DIC $dic) {
    return new Makaira\Connect\Repository\CategoryRepository(
        $dic['oxid.database'], new Makaira\Connect\Repository\ModifierList(
            $dic->getTagged('makaira.importer.modifier.category')
        )
    );
};

// --------------------------------------

$dic['makaira.connect.modifiers.common.product2shop'] = function (\Marm\Yamm\DIC $dic) {
    return new \Makaira\Connect\Modifier\Common\Product2ShopModifier(
        $dic['oxid.database'], oxRegistry::getConfig()->isMall()
    );
};
$dic->tag('makaira.connect.modifiers.common.product2shop', 'makaira.importer.modifier.product');
$dic->tag('makaira.connect.modifiers.common.product2shop', 'makaira.importer.modifier.variant');

$dic['makaira.connect.modifiers.common.attribute'] = function (\Marm\Yamm\DIC $dic) {
    return new \Makaira\Connect\Modifier\Common\AttributeModifier(
        $dic['oxid.database']
    );
};
$dic->tag('makaira.connect.modifiers.common.attribute', 'makaira.importer.modifier.product');
$dic->tag('makaira.connect.modifiers.common.attribute', 'makaira.importer.modifier.variant');

$dic['makaira.connect.modifiers.common.vat'] = function (\Marm\Yamm\DIC $dic) {
    $config = oxRegistry::getConfig();

    return new \Makaira\Connect\Modifier\Common\PriceModifier(
        $config->getConfigParam('blEnterNetPrice'),
        $config->getConfigParam('blShowNetPrice'),
        $config->getConfigParam('dDefaultVAT')
    );
};
$dic->tag('makaira.connect.modifiers.common.vat', 'makaira.importer.modifier.product');
$dic->tag('makaira.connect.modifiers.common.vat', 'makaira.importer.modifier.variant');

$dic['makaira.connect.modifiers.common.active'] = function (\Marm\Yamm\DIC $dic) {
    return new \Makaira\Connect\Modifier\Common\ActiveModifier();
};
$dic->tag('makaira.connect.modifiers.common.active', 'makaira.importer.modifier.product');
$dic->tag('makaira.connect.modifiers.common.active', 'makaira.importer.modifier.variant');

$dic['makaira.connect.modifiers.common.longdescription'] = function (\Marm\Yamm\DIC $dic) {
    return new \Makaira\Connect\Modifier\Common\LongDescriptionModifier($dic['makaira.content_parser']);
};
$dic->tag('makaira.connect.modifiers.common.longdescription', 'makaira.importer.modifier.product');
$dic->tag('makaira.connect.modifiers.common.longdescription', 'makaira.importer.modifier.variant');

// --------------------------------------

$dic['makaira.connect.modifiers.product.suggest'] = function (\Marm\Yamm\DIC $dic) {
    return new \Makaira\Connect\Modifier\Product\SuggestModifier(
        [] // @TODO: Fill with sensible values
    );
};
$dic->tag('makaira.connect.modifiers.product.suggest', 'makaira.importer.modifier.product');

$dic['makaira.connect.modifiers.product.category'] = function (\Marm\Yamm\DIC $dic) {
    return new \Makaira\Connect\Modifier\Product\CategoryModifier(
        $dic['oxid.database'], false // @TODO:Remove flag, move logic to query
    );
};
$dic->tag('makaira.connect.modifiers.product.category', 'makaira.importer.modifier.product');

// @DEPRECATED: Will be removed
$dic['makaira.connect.modifiers.product.tracking'] = function (\Marm\Yamm\DIC $dic) {
    return new \Makaira\Connect\Modifier\Product\TrackingModifier(
        isset($dic['marm_oxsearch']) ? $dic['marm_oxsearch']['tracking'] : null
    );
};
$dic->tag('makaira.connect.modifiers.product.tracking', 'makaira.importer.modifier.product');

$dic['makaira.connect.modifiers.category.oxobject'] = function (\Marm\Yamm\DIC $dic) {
    return new \Makaira\Connect\Modifier\Category\OxObjectModifier(
        $dic['oxid.database']
    );
};
$dic->tag('makaira.connect.modifiers.category.oxobject', 'makaira.importer.modifier.category');

