<?php

namespace Makaira\Connect\Change\Product;


use Makaira\Connect\DatabaseInterface;

class ActiveModifierTest extends \PHPUnit_Framework_TestCase
{
    public function testActiveNotHidden()
    {
        $dbMock = $this->getMock(DatabaseInterface::class, ['query'], [], '', false);
        $product = new LegacyProduct();
        $product->OXACTIVE = "1";
        $product->OXHIDDEN = "0";
        $modifier = new ActiveModifier();
        $this->assertEquals(true, $modifier->apply($product, $dbMock)->active);
    }

    public function testNotActiveNotHidden()
    {
        $dbMock = $this->getMock(DatabaseInterface::class, ['query'], [], '', false);
        $product = new LegacyProduct();
        $product->OXACTIVE = "0";
        $product->OXHIDDEN = "0";
        $modifier = new ActiveModifier();
        $this->assertEquals(false, $modifier->apply($product, $dbMock)->active);
    }

    public function testNotActiveHidden()
    {
        $dbMock = $this->getMock(DatabaseInterface::class, ['query'], [], '', false);
        $product = new LegacyProduct();
        $product->OXACTIVE = "0";
        $product->OXHIDDEN = "1";
        $modifier = new ActiveModifier();
        $this->assertEquals(false, $modifier->apply($product, $dbMock)->active);
    }

    public function testActiveHidden()
    {
        $dbMock = $this->getMock(DatabaseInterface::class, ['query'], [], '', false);
        $product = new LegacyProduct();
        $product->OXACTIVE = "1";
        $product->OXHIDDEN = "1";
        $modifier = new ActiveModifier();
        $this->assertEquals(false, $modifier->apply($product, $dbMock)->active);
    }

    public function testActiveHiddenNull()
    {
        $dbMock = $this->getMock(DatabaseInterface::class, ['query'], [], '', false);
        $product = new LegacyProduct();
        $product->OXACTIVE = "1";
        $product->OXHIDDEN = null;
        $modifier = new ActiveModifier();
        $this->assertEquals(true, $modifier->apply($product, $dbMock)->active);
    }

    public function testNotActiveHiddenNull()
    {
        $dbMock = $this->getMock(DatabaseInterface::class, ['query'], [], '', false);
        $product = new LegacyProduct();
        $product->OXACTIVE = "0";
        $product->OXHIDDEN = null;
        $modifier = new ActiveModifier();
        $this->assertEquals(false, $modifier->apply($product, $dbMock)->active);
    }
}
