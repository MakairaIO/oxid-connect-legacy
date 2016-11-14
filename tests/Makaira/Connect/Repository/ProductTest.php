<?php

namespace Makaira\Connect\Repository;

use Makaira\Connect\Database;
use Makaira\Connect\Result\Changes;
use Makaira\Connect\Change;

class ProductTest extends \PHPUnit_Framework_TestCase
{
    public function testLoadProduct()
    {
        $databaseMock = $this->getMock(Database::class, ['query'], [], '', false);
        $repository = new Product($databaseMock);

        $databaseMock
            ->expects($this->once())
            ->method('query')
            ->will($this->returnValue([['id' => 42, 'sequence' => 1]]));

        $changes = $repository->getChangesSince(0);

        $this->assertEquals(
            new Changes(array(
                'type' => 'product',
                'since' => 0,
                'count' => 1,
                'changes' => array(
                    new Change(array(
                        'sequence' => 1,
                        'id' => 42,
                        'data' => new Change\LegacyProduct(array(
                            'id' => 42,
                        ))
                    ))
                ),
            )),
            $changes
        );
    }

    public function testRunModifierLoadProduct()
    {
        $databaseMock = $this->getMock(Database::class, ['query'], [], '', false);
        $modifierMock = $this->getMock(Change\Product\Modifier::class);

        $repository = new Product($databaseMock, [$modifierMock]);

        $databaseMock
            ->expects($this->once())
            ->method('query')
            ->will($this->returnValue([['id' => 42, 'sequence' => 1]]));

        $product = new \stdClass();
        $modifierMock
            ->expects($this->once())
            ->method('apply')
            ->will($this->returnValue($product));

        $changes = $repository->getChangesSince(0);

        $this->assertSame(
            $product,
            $changes->changes[0]->data
        );
    }
}
