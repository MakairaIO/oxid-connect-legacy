<?php

namespace Makaira\Connect\Repository;

use Makaira\Connect\Type;
use Makaira\Connect\Modifier;

class ModifierListTest extends \PHPUnit_Framework_TestCase
{
    public function testApplyModifier()
    {
        $modifier = $this->getMock(Modifier::class);
        $type = new Type();

        $modifier
            ->expects($this->once())
            ->method('apply')
            ->with($type)
            ->will($this->returnValue($type));

        $modifierList = new ModifierList([$modifier]);
        $result = $modifierList->applyModifiers($type);

        $this->assertSame($type, $result);
    }
}
