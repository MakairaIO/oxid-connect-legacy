<?php

namespace Makaira\Connect\Types\Common;


use Makaira\Connect\Types\Common\BaseProduct;
use Makaira\Connect\DatabaseInterface;

class LongDescriptionModifierTest extends \PHPUnit_Framework_TestCase
{

    public function testShortText()
    {
        $dbMock = $this->getMock(DatabaseInterface::class, ['query'], [], '', false);
        $modifier = new LongDescriptionModifier();
        $product = new BaseProduct();
        $product->OXLONGDESC = 'This is a short text';
        $product = $modifier->apply($product, $dbMock);
        $this->assertEquals('This is a short text', $product->OXLONGDESC);
    }

    public function testShortTextWithHTML()
    {
        $dbMock = $this->getMock(DatabaseInterface::class, ['query'], [], '', false);
        $modifier = new LongDescriptionModifier();
        $product = new BaseProduct();
        $product->OXLONGDESC = 'This is a <del>short</del> text';
        $product = $modifier->apply($product, $dbMock);
        $this->assertEquals('This is a short text', $product->OXLONGDESC);
    }

}
