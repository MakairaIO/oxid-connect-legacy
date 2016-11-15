<?php
/**
 * Created by PhpStorm.
 * User: benjamin
 * Date: 15.11.16
 * Time: 13:48
 */

namespace Makaira\Connect\Change\Product;


use Makaira\Connect\DatabaseInterface;

class TrackingModifierTest extends \PHPUnit_Framework_TestCase
{

    public function testTracking()
    {
        $dbMock = $this->getMock(DatabaseInterface::class, ['query'], [], '', false);
        $trackingMock = $this->getMock(\AbstractOxSearchTracking::class, [], [], '', false);
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
        $product = new LegacyProduct();
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
