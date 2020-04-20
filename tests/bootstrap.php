<?php

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

require_once __DIR__ . '/Makaira/Connect/IntegrationTest.php';


if (!class_exists('oxDb')) {
    require_once __DIR__ . '/Makaira/Connect/OxidMocks/oxDb.php';
}

if (!class_exists('oxRegistry')) {
    require_once __DIR__ . '/Makaira/Connect/OxidMocks/oxRegistry.php';
    require_once __DIR__ . '/Makaira/Connect/OxidMocks/core.php';
    require_once __DIR__ . '/Makaira/Connect/OxidMocks/console.php';
}

if (!function_exists('oxNew')) {
    require_once __DIR__ . '/Makaira/Connect/OxidMocks/functions.php';
}

if (!class_exists('OxidEsales\\EshopCommunity\\Internal\\Container\\ContainerFactory')) {
    require_once __DIR__ . '/Makaira/Connect/OxidMocks/ContainerFactory.php';
}