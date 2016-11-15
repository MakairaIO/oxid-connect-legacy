<?php
/**
 * Created by PhpStorm.
 * User: benjamin
 * Date: 15.11.16
 * Time: 11:12
 */

namespace Makaira\Connect\Change\Product;


use Makaira\Connect\Change\Common\Category;
use Makaira\Connect\DatabaseInterface;

class CategoryModifierTest extends \PHPUnit_Framework_TestCase
{

    public function testUnnested()
    {
        $dbMock = $this->getMock(DatabaseInterface::class, ['query'], [], '', false);
        $dbMock
            ->expects($this->once())
            ->method('query')
            ->with($this->anything(), ['productId' => 'abc', 'productActive' => 1])
            ->will(
                $this->returnValue(
                    [
                        [
                            'catid'  => 'def',
                            'oxpos'  => 1,
                            'shopid' => 1,
                        ],
                    ]
                )
            );

        $product = new LegacyProduct();
        $product->id = 'abc';
        $product->OXACTIVE = 1;

        $modifier = new CategoryModifier(false);

        $product = $modifier->apply($product, $dbMock);

        $this->assertEquals(
            [
                new Category(
                    [
                        'catid'  => 'def',
                        'oxpos'  => 1,
                        'shopid' => 1,
                    ]
                ),
            ], $product->category
        );
    }

    public function testNested()
    {
        $dbMock = $this->getMock(DatabaseInterface::class, ['query'], [], '', false);
        $dbMock
            ->expects($this->exactly(3))
            ->method('query')
            ->withConsecutive(
                [$this->anything(), $this->equalTo(['productId' => 'abc', 'productActive' => 1])],
                [$this->anything(), $this->equalTo(['catid' => 'def'])],
                [$this->anything(), $this->equalTo(['oxrootid' => 1, 'oxleft' => 2, 'oxright' => 3, 'shopid' => 1])]
            )
            ->willReturnOnConsecutiveCalls(
                $this->returnValue(
                    [
                        [
                            'catid'  => 'def',
                            'oxpos'  => 1,
                            'shopid' => 1,
                        ],
                    ]
                ),
                $this->returnValue(
                    [
                        [
                            'oxrootid' => 1,
                            'oxleft'   => 2,
                            'oxright'  => 3,
                        ],
                    ]
                ),
                $this->returnValue(
                    [
                        [
                            'catid'  => 'ghi',
                            'oxpos'  => 9999,
                            'shopid' => 1,
                        ],
                    ]
                )
            );

        $product = new LegacyProduct();
        $product->id = 'abc';
        $product->OXACTIVE = 1;

        $modifier = new CategoryModifier(true);

        $product = $modifier->apply($product, $dbMock);

        $this->assertEquals(
            [
                new Category(
                    [
                        'catid'  => 'def',
                        'oxpos'  => 1,
                        'shopid' => 1,
                    ]
                ),
                new Category(
                    [
                        'catid'  => 'ghi',
                        'oxpos'  => 9999,
                        'shopid' => 1,
                    ]
                ),
            ], $product->category
        );
    }

}
