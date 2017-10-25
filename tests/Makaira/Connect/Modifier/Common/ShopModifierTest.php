<?php

namespace Makaira\Connect\Modifier\Common;

use Makaira\Connect\DatabaseInterface;
use Makaira\Connect\Type\Common\BaseProduct;
use Makaira\Connect\Type\Product\Product;

class ShopModifierTest extends \PHPUnit_Framework_TestCase
{
    public function testCEPE()
    {
        $dbMock = $this->getMock(DatabaseInterface::class);
        $dbMock
            ->expects($this->never())
            ->method('query');
        $product = new BaseProduct();
        $product->OXSHOPID = 'test';
        $modifier = new ShopModifier($dbMock, false, 'oxarticles2shop');
        $product = $modifier->apply($product);
        $this->assertEquals(['test'], $product->shop);
    }

    public function testEE()
    {
        $dbMock = $this->getMock(DatabaseInterface::class);
        $dbMock
            ->expects($this->once())
            ->method('query')
            ->with($this->anything(), ['mapId' => 1])
            ->will($this->returnValue([['OXSHOPID' => 1], ['OXSHOPID' => 2]]));
        $product = new BaseProduct();
        $product->OXMAPID = 1;
        $modifier = new ShopModifier($dbMock, true, 'oxarticles2shop');
        $product = $modifier->apply($product);
        $this->assertEquals([1, 2], $product->shop);
    }

    public function testLegacyEE()
    {
        $dbMock = $this->getMock(DatabaseInterface::class);
        $product = new Product();
        $product->OXSHOPINCL = 3;
        $modifier = new ShopModifier($dbMock, true, 'oxarticles2shop');
        $product = $modifier->apply($product);
        $this->assertEquals([1, 2], $product->shop);
    }
}
