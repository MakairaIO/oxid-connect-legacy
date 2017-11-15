<?php

$autoloadLocations = [
    __DIR__ . '/vendor/autoload.php',
    __DIR__ . '/../../../vendor/autoload.php',
    __DIR__ . '/../../../../vendor/autoload.php',
];

foreach ($autoloadLocations as $autoloadLocation) {
    if (file_exists($autoloadLocation)) {
        require_once $autoloadLocation;
        break;
    }
}

/** @var \Marm\Yamm\DIC $dic */

$dic['doctrine.connection'] = function (\Marm\Yamm\DIC $dic) {
    $config = oxRegistry::getConfig();
    $connectionParams = array(
		'host' => $config->getConfigParam('dbHost'),
		'dbname' => $config->getConfigParam('dbName'),
		'user' => $config->getConfigParam('dbUser'),
		'password' => $config->getConfigParam('dbPwd'),
		'driver' => 'pdo_mysql',
		'charset' => 'utf8',
	);
	return \Doctrine\DBAL\DriverManager::getConnection($connectionParams);
};

$dic['oxid.database'] = function (\Marm\Yamm\DIC $dic) {
    return new \Makaira\Connect\Database\DoctrineDatabase(
        $dic['doctrine.connection'],
        $dic['oxid.table_translator']
    );
};

$dic['oxid.language'] = function (\Marm\Yamm\DIC $dic) {
    return oxRegistry::getLang();
};

$dic['content_parsers.oxid.smarty'] = function (\Marm\Yamm\DIC $dic) {
    return new \Makaira\Connect\Utils\OxidSmartyParser(
        $dic['oxid.language'], oxRegistry::get('oxutilsview')
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

$dic['makaira.connect.utils.tokengenerator'] = function (\Marm\Yamm\DIC $dic) {
    return new Makaira\Connect\Utils\TokenGenerator();
};

// --------------------------------------

$dic['makaira.connect.repository.user'] = function (\Marm\Yamm\DIC $dic) {
    return new Makaira\Connect\Repository\UserRepository(
        $dic['oxid.database']
    );
};

// --------------------------------------

$dic['makaira.connect.repository'] = function (\Marm\Yamm\DIC $dic) {
    $repositories = [];
    /** @var \Makaira\Connect\RepositoryInterface $repository */
    foreach ($dic->getTagged('makaira.connect.repository') as $repository) {
        $repositories[$repository->getType()] = $repository;
    }
    return new Makaira\Connect\Repository(
        $dic['oxid.database'], $repositories
    );
};

$dic['makaira.connect.repository.product'] = function (\Marm\Yamm\DIC $dic) {
    return new Makaira\Connect\Repository\ProductRepository(
        $dic['oxid.database'], new Makaira\Connect\Repository\ModifierList(
            $dic->getTagged('makaira.importer.modifier.product')
        )
    );
};
$dic->tag('makaira.connect.repository.product', 'makaira.connect.repository');

$dic['makaira.connect.repository.variant'] = function (\Marm\Yamm\DIC $dic) {
    return new Makaira\Connect\Repository\VariantRepository(
        $dic['oxid.database'], new Makaira\Connect\Repository\ModifierList(
            $dic->getTagged('makaira.importer.modifier.variant')
        )
    );
};
$dic->tag('makaira.connect.repository.variant', 'makaira.connect.repository');

$dic['makaira.connect.repository.category'] = function (\Marm\Yamm\DIC $dic) {
    return new Makaira\Connect\Repository\CategoryRepository(
        $dic['oxid.database'], new Makaira\Connect\Repository\ModifierList(
            $dic->getTagged('makaira.importer.modifier.category')
        )
    );
};
$dic->tag('makaira.connect.repository.category', 'makaira.connect.repository');

$dic['makaira.connect.repository.manufacturer'] = function (\Marm\Yamm\DIC $dic) {
    return new Makaira\Connect\Repository\ManufacturerRepository(
        $dic['oxid.database'], new Makaira\Connect\Repository\ModifierList(
            $dic->getTagged('makaira.importer.modifier.manufacturer')
        )
    );
};
$dic->tag('makaira.connect.repository.manufacturer', 'makaira.connect.repository');

// --------------------------------------

$dic['makaira.connect.modifiers.common.product2shop'] = function (\Marm\Yamm\DIC $dic) {
    return new \Makaira\Connect\Modifier\Common\ShopModifier(
        $dic['oxid.database'], oxRegistry::getConfig()->isMall(), 'oxarticles2shop'
    );
};
$dic->tag('makaira.connect.modifiers.common.product2shop', 'makaira.importer.modifier.product');
$dic->tag('makaira.connect.modifiers.common.product2shop', 'makaira.importer.modifier.variant');

$dic['makaira.connect.modifiers.common.category2shop'] = function (\Marm\Yamm\DIC $dic) {
    return new \Makaira\Connect\Modifier\Common\ShopModifier(
        $dic['oxid.database'], oxRegistry::getConfig()->isMall(), 'oxcategories2shop'
    );
};
$dic->tag('makaira.connect.modifiers.common.category2shop', 'makaira.importer.modifier.category');

$dic['makaira.connect.modifiers.common.manufacturer2shop'] = function (\Marm\Yamm\DIC $dic) {
    return new \Makaira\Connect\Modifier\Common\ShopModifier(
        $dic['oxid.database'], oxRegistry::getConfig()->isMall(), 'oxmanufacturers2shop'
    );
};
$dic->tag('makaira.connect.modifiers.common.manufacturer2shop', 'makaira.importer.modifier.manufacturer');

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

$dic['makaira.connect.modifiers.common.zerodatetime'] = function (\Marm\Yamm\DIC $dic) {
    return new \Makaira\Connect\Modifier\Common\ZeroDateTimeModifier();
};
$dic->tag('makaira.connect.modifiers.common.zerodatetime', 'makaira.importer.modifier.product');
$dic->tag('makaira.connect.modifiers.common.zerodatetime', 'makaira.importer.modifier.variant');
$dic->tag('makaira.connect.modifiers.common.zerodatetime', 'makaira.importer.modifier.category');

$dic['makaira.connect.modifiers.category.url'] = function (\Marm\Yamm\DIC $dic) {
    return new \Makaira\Connect\Modifier\Common\UrlModifier(
        oxRegistry::get('oxCategory'),
        oxRegistry::get('oxSeoEncoderCategory'),
        $dic['oxid.language']
    );
};
$dic['makaira.connect.modifiers.manufacturer.url'] = function (\Marm\Yamm\DIC $dic) {
    return new \Makaira\Connect\Modifier\Common\UrlModifier(
        oxRegistry::get('oxManufacturer'),
        oxRegistry::get('oxSeoEncoderManufacturer'),
        $dic['oxid.language']
    );
};
$dic['makaira.connect.modifiers.product.url'] = function (\Marm\Yamm\DIC $dic) {
    return new \Makaira\Connect\Modifier\Common\UrlModifier(
        oxRegistry::get('oxArticle'),
        oxRegistry::get('oxSeoEncoderArticle'),
        $dic['oxid.language']
    );
};
$dic->tag('makaira.connect.modifiers.category.url', 'makaira.importer.modifier.category');
$dic->tag('makaira.connect.modifiers.manufacturer.url', 'makaira.importer.modifier.manufacturer');
$dic->tag('makaira.connect.modifiers.product.url', 'makaira.importer.modifier.product');

$dic['makaira.connect.modifiers.common.blacklist.product'] = function (\Marm\Yamm\DIC $dic) {
    return new \Makaira\Connect\Modifier\Common\BlacklistModifier(
        (array) oxRegistry::getConfig()->getShopConfVar('makaira_field_blacklist_product')
    );
};
$dic['makaira.connect.modifiers.common.blacklist.category'] = function (\Marm\Yamm\DIC $dic) {
    return new \Makaira\Connect\Modifier\Common\BlacklistModifier(
        (array) oxRegistry::getConfig()->getShopConfVar('makaira_field_blacklist_category')
    );
};
$dic['makaira.connect.modifiers.common.blacklist.manufacturer'] = function (\Marm\Yamm\DIC $dic) {
    return new \Makaira\Connect\Modifier\Common\BlacklistModifier(
        (array) oxRegistry::getConfig()->getShopConfVar('makaira_field_blacklist_manufacturer')
    );
};
$dic->tag('makaira.connect.modifiers.common.blacklist.product', 'makaira.importer.modifier.product');
$dic->tag('makaira.connect.modifiers.common.blacklist.category', 'makaira.importer.modifier.category');
$dic->tag('makaira.connect.modifiers.common.blacklist.manufacturer', 'makaira.importer.modifier.manufacturer');


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

$dic['makaira.connect.modifiers.category.hierarchy'] = function (\Marm\Yamm\DIC $dic) {
    return new \Makaira\Connect\Modifier\Category\HierarchyModifier(
        $dic['oxid.database']
    );
};
$dic->tag('makaira.connect.modifiers.category.hierarchy', 'makaira.importer.modifier.category');

//------------------------------
$dic['makaira.connect.configuration'] = function (\Marm\Yamm\DIC $dic) {
    return new Makaira\ConnectionConfiguration([
        'url' => oxRegistry::getConfig()->getShopConfVar(
            'makaira_application_url',
            null,
            oxConfig::OXMODULE_MODULE_PREFIX . 'makaira/connect'
        ),
        'secret' => oxRegistry::getConfig()->getShopConfVar(
            'makaira_connect_secret',
            null,
            oxConfig::OXMODULE_MODULE_PREFIX . 'makaira/connect'
        ),
    ]);
};

$dic['makaira.connect.http_client'] = function (\Marm\Yamm\DIC $dic) {
    $configuration = $dic['makaira.connect.configuration'];
    $timeout = oxRegistry::getConfig()->getConfigParam('makairaConnectTimeout') ?: 1;

    return new Makaira\HttpClient\Signing(
        new Makaira\HttpClient\Curl($timeout),
        $configuration->secret
    );
};

$dic['makaira.connect.searchhandler'] = function (\Marm\Yamm\DIC $dic) {
    $configuration = $dic['makaira.connect.configuration'];
    return new \Makaira\Connect\SearchHandler(
        $dic['makaira.connect.http_client'],
        $configuration->url,
        oxRegistry::getConfig()->getShopConfVar(
            'makaira_instance',
            null,
            oxConfig::OXMODULE_MODULE_PREFIX . 'makaira/connect'
        )
    );
};

$dic['makaira.connect.suggester'] = function (\Marm\Yamm\DIC $dic) {
    return oxNew('makaira_connect_autosuggester', oxRegistry::getLang());
};
