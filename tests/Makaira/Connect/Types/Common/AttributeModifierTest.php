<?php

namespace Makaira\Connect\Types\Common;


use Makaira\Connect\Types\Common\Attribute;
use Makaira\Connect\Types\Common\BaseProduct;
use Makaira\Connect\DatabaseInterface;

class AttributeModifierTest extends \PHPUnit_Framework_TestCase
{

    public function testApply()
    {
        $dbMock = $this->getMock(DatabaseInterface::class, ['query'], [], '', false);
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

        $product = $modifier->apply(new BaseProduct(['id' => $oxid, 'OXACTIVE' => 1]), $dbMock);

        $this->assertArraySubset([new Attribute($dbResult)], $product->attribute);
    }
}
