<?php

namespace Makaira\Connect;

if (!class_exists('oxDb')) {
    require_once __DIR__ . '/OxidMocks/oxDb.php';
}

if (!class_exists('oxRegistry')) {
    require_once __DIR__ . '/OxidMocks/oxRegistry.php';
}

abstract class IntegrationTest extends \PHPUnit_Framework_TestCase
{
    protected function getContainer()
    {
        $dic = new \Marm\Yamm\DIC();
        require __DIR__ . '/../../../dic.php';

        return $dic;
    }
}
