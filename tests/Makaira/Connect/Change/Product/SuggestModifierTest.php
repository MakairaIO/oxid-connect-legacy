<?php
/**
 * Created by PhpStorm.
 * User: benjamin
 * Date: 14.11.16
 * Time: 16:49
 */

namespace Makaira\Connect\Change\Product;


use Makaira\Connect\Database;

class SuggestModifierTest extends \PHPUnit_Framework_TestCase
{
    public function testModifier()
    {
        $dbMock = $this->getMock(Database::class, ['query'], [], '', false);
        $modifier = new SuggestModifier(['OXTITLE', 'OXTAGS']);
        $product = new LegacyProduct();
        $product->OXTITLE = 'Test case';
        $product->OXTAGS = 'test, case, Test case';
        $product = $modifier->apply($product, $dbMock);
        $this->assertEquals(['Test case', 'test', 'case'], $product->suggest);
    }
}
