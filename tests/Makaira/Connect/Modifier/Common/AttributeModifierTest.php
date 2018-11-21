<?php

namespace Makaira\Connect\Modifier\Common;

use Makaira\Connect\Type\Common\AssignedAttribute;
use Makaira\Connect\Type\Common\BaseProduct;
use Makaira\Connect\DatabaseInterface;

class AttributeModifierTest extends \PHPUnit_Framework_TestCase
{

    public function testApply()
    {
        $dbMock = $this->getMock(DatabaseInterface::class);
        $oxid = 'abcdef';
        $dbResult = [
            'active' => 1,
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

        $product = $modifier->apply(new BaseProduct(['id' => $oxid, 'OXACTIVE' => 1]));

        $this->assertArraySubset(
            [new AssignedAttribute([
                 'active'  => $dbResult['active'],
                 'oxid'    => $dbResult['id'],
                 'oxtitle' => $dbResult['title'],
                 'oxvalue' => $dbResult['value'],
            ])],
            $product->attribute
        );
    }
}
