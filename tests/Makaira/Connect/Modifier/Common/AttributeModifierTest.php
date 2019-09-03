<?php

namespace Makaira\Connect\Modifier\Common;

use Makaira\Connect\Type\Common\AssignedTypedAttribute;
use Makaira\Connect\Type\Common\BaseProduct;
use Makaira\Connect\DatabaseInterface;

class TypedAttributeModifierTest extends \PHPUnit_Framework_TestCase
{

    public function testApply()
    {
        $dbMock = $this->getMock(DatabaseInterface::class);
        $oxid = 'abcdef';
        $dbResult = [
            'id' => $oxid,
            'title' => 'abcdef',
            'value' => 'abcdef'
        ];
        $dbMock
            ->expects($this->at(0))
            ->method('query')
            ->will($this->returnValue([$dbResult]));

        $dbMock
            ->expects($this->at(1))
            ->method('query')
            ->will($this->returnValue([]));

        $dbMock
            ->expects($this->at(2))
            ->method('query')
            ->will($this->returnValue([["oxvarname" => "qwert"]]));

        $modifier = new AttributeModifier($dbMock, '1', [], []);

        $product = $modifier->apply(new BaseProduct(['id' => $oxid, 'active' => 1]));

        $this->assertArraySubset(
            [new AssignedTypedAttribute([
                 'id'    => $dbResult['id'],
                 'title' => $dbResult['title'],
                 'value' => $dbResult['value'],
            ])],
            $product->attributeStr
        );
    }
}
