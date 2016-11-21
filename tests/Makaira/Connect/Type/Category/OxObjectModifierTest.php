<?php

namespace Makaira\Connect\Type\Category;


use Makaira\Connect\DatabaseInterface;

class OxObjectModifierTest extends \PHPUnit_Framework_TestCase
{

    public function testApply()
    {
        $dbMock = $this->getMock(DatabaseInterface::class, ['query'], [], '', false);
        $dbResult = [
            'oxid' => 'abcdef',
            'oxpos' => 42,
        ];
        $dbMock
            ->expects($this->atLeastOnce())
            ->method('query')
            ->will($this->returnValue([$dbResult]));

        $modifier = new OxObjectModifier();

        $category = $modifier->apply(new Category(['id' => 'ghijkl']), $dbMock);

        $this->assertArraySubset([new AssignedOxObject($dbResult)], $category->oxobject);
    }
}
