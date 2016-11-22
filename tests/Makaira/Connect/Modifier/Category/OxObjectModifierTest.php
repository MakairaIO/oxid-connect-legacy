<?php

namespace Makaira\Connect\Modifier\Category;

use Makaira\Connect\DatabaseInterface;
use Makaira\Connect\Type\Category\Category;
use Makaira\Connect\Type\Category\AssignedOxObject;

class OxObjectModifierTest extends \PHPUnit_Framework_TestCase
{

    public function testApply()
    {
        $dbMock = $this->getMock(DatabaseInterface::class);
        $dbResult = [
            'oxid' => 'abcdef',
            'oxpos' => 42,
        ];
        $dbMock
            ->expects($this->atLeastOnce())
            ->method('query')
            ->will($this->returnValue([$dbResult]));

        $modifier = new OxObjectModifier($dbMock);

        $category = $modifier->apply(new Category(['id' => 'ghijkl']));

        $this->assertArraySubset([new AssignedOxObject($dbResult)], $category->oxobject);
    }
}
