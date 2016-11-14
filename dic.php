<?php

require_once __DIR__ . '/vendor/autoload.php';

/** @var \Marm\Yamm\DIC $dic */

$dic['oxid.database'] = function (\Marm\Yamm\DIC $dic) {
    return new \Makaira\Connect\Database(
        oxDb::getInstance()->getDb()
    );
};

$dic['makaira.connect.repository.product'] = function (\Marm\Yamm\DIC $dic) {
    return new Makaira\Connect\Repository\Product(
        $dic['oxid.database'],
        $dic->getTagged('makaira.importer.modifier.product')
    );
};

$dic['makaira.connect.modifiers.product.attribute'] = function (\Marm\Yamm\DIC $dic) {
    return new \Makaira\Connect\Change\Product\AttributeModifier();
};
$dic->tag('makaira.connect.modifiers.product.attribute', 'makaira.importer.modifier.product');

$dic['makaira.connect.modifiers.product.vat'] = function (\Marm\Yamm\DIC $dic) {
    $config = oxRegistry::getConfig();
    return new \Makaira\Connect\Change\Product\PriceModifier(
        $config->getConfigParam('blEnterNetPrice'),
        $config->getConfigParam('blShowNetPrice'),
        $config->getConfigParam('dDefaultVAT')
    );
};
$dic->tag('makaira.connect.modifiers.product.vat', 'makaira.importer.modifier.product');

$dic['makaira.connect.modifiers.product.active'] = function (\Marm\Yamm\DIC $dic) {
    return new \Makaira\Connect\Change\Product\ActiveModifier();
};
$dic->tag('makaira.connect.modifiers.product.active', 'makaira.importer.modifier.product');

$dic['makaira.connect.modifiers.product.suggest'] = function (\Marm\Yamm\DIC $dic) {
    return new \Makaira\Connect\Change\Product\SuggestModifier(
        $dic['marm_oxsearch']['oxsearch_configuration']['search']['suggestfields']
    );
};
$dic->tag('makaira.connect.modifiers.product.suggest', 'makaira.importer.modifier.product');

