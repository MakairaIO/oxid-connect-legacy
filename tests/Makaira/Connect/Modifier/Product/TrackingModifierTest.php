<?php

namespace Makaira\Connect\Modifier\Product;

use Makaira\Connect\DatabaseInterface;
use Makaira\Connect\Type\Product\Product;

class TrackingModifierTest extends \PHPUnit_Framework_TestCase
{

    public function testTracking()
    {
        $dbMock = $this->getMock(DatabaseInterface::class);
        $trackingMock = $this->getMock(
            \AbstractOxSearchTracking::class, [
            'get',
            'increase',
            'decrease',
            'set',
            'isInitialized',
            'initialize',
            'countTrackedObjects',
            'listTrackedObjects',
        ], [], '', false
        );
        $trackingMock
            ->expects($this->once())
            ->method('get')
            ->with('product', 'abc')
            ->will(
                $this->returnValue(
                    [
                        'rated'     => 1,
                        'basketed'  => 2,
                        'requested' => 3,
                        'sold'      => 4,
                    ]
                )
            );
        $modifier = new TrackingModifier($trackingMock);
        $product = new Product();
        $product->id = 'abc';
        $product = $modifier->apply($product, $dbMock);

        $this->assertEquals(1, $product->OXRATINGCNT);
        $this->assertEquals(2, $product->MARM_OXSEARCH_BASKETCOUNT);
        $this->assertEquals(3, $product->MARM_OXSEARCH_REQCOUNT);
        $this->assertEquals(4, $product->OXSOLDAMOUNT);
        $this->assertEquals(
            [
                'rated'     => 1,
                'basketed'  => 2,
                'requested' => 3,
                'sold'      => 4,
            ], $product->TRACKING
        );
    }

}
