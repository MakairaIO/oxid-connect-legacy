<?php

namespace Makaira\Connect\Modifier\Common;

use Makaira\Connect\Type;

class ZeroDateTimeModifierTest extends \PHPUnit_Framework_TestCase
{
    public function testValidDateTime()
    {
        $modifier = new ZeroDateTimeModifier();

        $type = new Type();
        $type->timestamp = "2016-01-01 00:00:00";
        $this->assertEquals("2016-01-01 00:00:00", $modifier->apply($type)->timestamp);

        $type = new Type();
        $type->timestamp = "2016-01-01";
        $this->assertEquals("2016-01-01", $modifier->apply($type)->timestamp);
    }

    public function testInvalidDateTime()
    {
        $modifier = new ZeroDateTimeModifier();

        $type = new Type();
        $type->timestamp = "0000-00-00 00:00:00";
        $this->assertEquals(null, $modifier->apply($type)->timestamp);

        $type = new Type();
        $type->timestamp = "0000-00-00";
        $this->assertEquals(null, $modifier->apply($type)->timestamp);
    }

    public function testNonDateValues()
    {
        $modifier = new ZeroDateTimeModifier();
        $stringTestValue = 'some string';
        $arrayTestValue = [1,2,3];
        $boolTestValue = false;

        $type = new Type();
        $type->id = $stringTestValue;
        $type->active = $boolTestValue;
        $type->shop = $arrayTestValue;

        $this->assertEquals($stringTestValue, $modifier->apply($type)->id);

        $this->assertEquals($boolTestValue, $modifier->apply($type)->active);

        $this->assertEquals($arrayTestValue, $modifier->apply($type)->shop);
    }
}
