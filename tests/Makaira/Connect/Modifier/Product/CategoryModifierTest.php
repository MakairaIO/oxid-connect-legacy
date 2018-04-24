<?php

namespace Makaira\Connect\Modifier\Product;

use Makaira\Connect\Type\Common\AssignedCategory;
use Makaira\Connect\Type\Product\Product;
use Makaira\Connect\DatabaseInterface;

class CategoryModifierTest extends \PHPUnit_Framework_TestCase
{
    public function testUnnested()
    {
        $dbMock = $this->getMock(DatabaseInterface::class);
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

        $product = new Product();
        $product->id = 'abc';
        $product->OXACTIVE = 1;

        $modifier = new CategoryModifier($dbMock);

        $product = $modifier->apply($product);

        $this->assertEquals(
            [
                new AssignedCategory(
                    [
                        'catid'  => 'def',
                        'oxpos'  => 1,
                        'shopid' => 1,
                    ]
                ),
            ], $product->category
        );
    }
}
