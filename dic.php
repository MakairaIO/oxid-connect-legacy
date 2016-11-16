<?php

require_once __DIR__ . '/vendor/autoload.php';

/** @var \Marm\Yamm\DIC $dic */

$dic['oxid.database'] = function (\Marm\Yamm\DIC $dic) {
    return new \Makaira\Connect\OxidDatabase(
        oxDb::getInstance()->getDb()
    );
};

$dic['pdo.database'] = function (\Marm\Yamm\DIC $dic) {
    $config = oxRegistry::getConfig();
    return new \Makaira\Connect\PdoDatabase(
        $config->getConfigParam('dbHost'),
        $config->getConfigParam('dbName'),
        $config->getConfigParam('dbUser'),
        $config->getConfigParam('dbPwd'),
        $config->isUtf()
    );
};

$dic['makaira.database'] = function (\Marm\Yamm\DIC $dic) {
    return $dic['pdo.database'];
};

$dic['makaira.connect.repository.product'] = function (\Marm\Yamm\DIC $dic) {
    return new Makaira\Connect\Repository\ProductRepository(
        $dic['makaira.database'],
        $dic->getTagged('makaira.importer.modifier.product')
    );
};

$dic['makaira.connect.repository.variant'] = function (\Marm\Yamm\DIC $dic) {
    return new Makaira\Connect\Repository\VariantRepository(
        $dic['makaira.database'],
        $dic->getTagged('makaira.importer.modifier.variant')
    );
};

// --------------------------------------

$dic['makaira.connect.modifiers.common.product2shop'] = function (\Marm\Yamm\DIC $dic) {
    return new \Makaira\Connect\Type\Common\Product2ShopModifier(oxRegistry::getConfig()->isMall());
};
$dic->tag('makaira.connect.modifiers.common.product2shop', 'makaira.importer.modifier.product');
$dic->tag('makaira.connect.modifiers.common.product2shop', 'makaira.importer.modifier.variant');

$dic['makaira.connect.modifiers.common.attribute'] = function (\Marm\Yamm\DIC $dic) {
    return new \Makaira\Connect\Type\Common\AttributeModifier();
};
$dic->tag('makaira.connect.modifiers.common.attribute', 'makaira.importer.modifier.product');
$dic->tag('makaira.connect.modifiers.common.attribute', 'makaira.importer.modifier.variant');

$dic['makaira.connect.modifiers.common.vat'] = function (\Marm\Yamm\DIC $dic) {
    $config = oxRegistry::getConfig();
    return new \Makaira\Connect\Type\Common\PriceModifier(
        $config->getConfigParam('blEnterNetPrice'),
        $config->getConfigParam('blShowNetPrice'),
        $config->getConfigParam('dDefaultVAT')
    );
};
$dic->tag('makaira.connect.modifiers.common.vat', 'makaira.importer.modifier.product');
$dic->tag('makaira.connect.modifiers.common.vat', 'makaira.importer.modifier.variant');

$dic['makaira.connect.modifiers.common.active'] = function (\Marm\Yamm\DIC $dic) {
    return new \Makaira\Connect\Type\Common\ActiveModifier();
};
$dic->tag('makaira.connect.modifiers.common.active', 'makaira.importer.modifier.product');
$dic->tag('makaira.connect.modifiers.common.active', 'makaira.importer.modifier.variant');

$dic['makaira.connect.modifiers.common.longdescription'] = function (\Marm\Yamm\DIC $dic) {
    return new \Makaira\Connect\Type\Common\LongDescriptionModifier();
};
$dic->tag('makaira.connect.modifiers.common.longdescription', 'makaira.importer.modifier.product');
$dic->tag('makaira.connect.modifiers.common.longdescription', 'makaira.importer.modifier.variant');

// --------------------------------------

$dic['makaira.connect.modifiers.product.suggest'] = function (\Marm\Yamm\DIC $dic) {
    return new \Makaira\Connect\Type\Product\SuggestModifier(
        $dic['marm_oxsearch']['oxsearch_configuration']['search']['suggestfields']
    );
};
$dic->tag('makaira.connect.modifiers.product.suggest', 'makaira.importer.modifier.product');

$dic['makaira.connect.modifiers.product.category'] = function (\Marm\Yamm\DIC $dic) {
    return new \Makaira\Connect\Type\Product\CategoryModifier(
        $dic['marm_oxsearch']['oxsearch_configuration']['general']['extra']['deepCategories']
    );
};
$dic->tag('makaira.connect.modifiers.product.category', 'makaira.importer.modifier.product');

$dic['makaira.connect.modifiers.product.tracking'] = function (\Marm\Yamm\DIC $dic) {
    return new \Makaira\Connect\Type\Product\TrackingModifier($dic['marm_oxsearch']['tracking']);
};
$dic->tag('makaira.connect.modifiers.product.tracking', 'makaira.importer.modifier.product');

