<?php

namespace Makaira\Connect\Types\Product;


use Makaira\Connect\DatabaseInterface;

class SuggestModifierTest extends \PHPUnit_Framework_TestCase
{
    public function testModifier()
    {
        $dbMock = $this->getMock(DatabaseInterface::class, ['query'], [], '', false);
        $modifier = new SuggestModifier(['OXTITLE', 'OXTAGS']);
        $product = new Product();
        $product->OXTITLE = 'Test case';
        $product->OXTAGS = 'test, case, Test case';
        $product = $modifier->apply($product, $dbMock);
        $this->assertEquals(['Test case', 'test', 'case'], $product->suggest);
    }
}
