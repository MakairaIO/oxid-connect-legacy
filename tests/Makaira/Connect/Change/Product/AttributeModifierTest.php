<?php

namespace Makaira\Connect\Change\Product;


use Makaira\Connect\Change\Common\Attribute;
use Makaira\Connect\Database;

class AttributeModifierTest extends \PHPUnit_Framework_TestCase
{

    public function testApply()
    {
        $dbMock = $this->getMock(Database::class, ['query'], [], '', false);
        $oxid = 'abcdef';
        $dbResult = [
            'active' => 1,
            'oxid' => $oxid,
            'oxtitle' => 'abcdef',
            'oxpos' => 0,
            'oxvalue' => 'abcdef'
        ];
        $dbMock
            ->expects($this->atLeastOnce())
            ->method('query')
            ->will($this->returnValue([$dbResult]));

        $modifier = new AttributeModifier();

        $product = $modifier->apply(new LegacyProduct(['id' => $oxid, 'OXACTIVE' => 1]), $dbMock);

        $this->assertArraySubset([new Attribute($dbResult)], $product->attribute);
    }
}
