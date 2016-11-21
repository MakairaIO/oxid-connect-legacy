<?php

namespace Makaira\Connect\Type\Common;


use Makaira\Connect\Type\Common\AssignedAttribute;
use Makaira\Connect\Type\Common\BaseProduct;
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

        $this->assertArraySubset([new AssignedAttribute($dbResult)], $product->attribute);
    }
}
