<?php

namespace Makaira\Connect;

use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;

abstract class IntegrationTest extends \PHPUnit_Framework_TestCase
{
    protected function getContainer()
    {
        return ContainerFactory::getInstance()->getContainer();
    }
}
