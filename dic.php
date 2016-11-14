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

