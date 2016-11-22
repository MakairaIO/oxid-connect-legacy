<?php

namespace Makaira\Connect\Modifier\Common;

use Makaira\Connect\DatabaseInterface;
use Makaira\Connect\Type\Common\BaseProduct;

class Product2ShopModifierTest extends \PHPUnit_Framework_TestCase
{
    public function testCEPE()
    {
        $dbMock = $this->getMock(DatabaseInterface::class, ['query'], [], '', false);
        $dbMock
            ->expects($this->never())
            ->method('query');
        $product = new BaseProduct();
        $product->OXSHOPID = 'test';
        $modifier = new Product2ShopModifier(false);
        $product = $modifier->apply($product, $dbMock);
        $this->assertEquals(['test'], $product->shop);
    }

    public function testEE()
    {
        $dbMock = $this->getMock(DatabaseInterface::class, ['query'], [], '', false);
        $dbMock
            ->expects($this->once())
            ->method('query')
            ->with($this->anything(), ['mapId' => 1])
            ->will($this->returnValue([['OXSHOPID' => 1], ['OXSHOPID' => 2]]));
        $product = new BaseProduct();
        $product->OXMAPID = 1;
        $modifier = new Product2ShopModifier(true);
        $product = $modifier->apply($product, $dbMock);
        $this->assertEquals([1, 2], $product->shop);
    }
}
