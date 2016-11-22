<?php

namespace Makaira\Connect\Repository;


use Makaira\Connect\Change;
use Makaira\Connect\DatabaseInterface;
use Makaira\Connect\Result\Changes;
use Makaira\Connect\Type\Category\Category;
use Makaira\Connect\Modifier;

class CategoryRepositoryTest extends \PHPUnit_Framework_TestCase
{
    public function testLoadProduct()
    {
        $databaseMock = $this->getMock(DatabaseInterface::class, ['query'], [], '', false);
        $repository = new CategoryRepository($databaseMock);

        $databaseMock
            ->expects($this->once())
            ->method('query')
            ->will($this->returnValue([['id' => 42, 'sequence' => 1, 'OXID' => 42]]));

        $changes = $repository->getChangesSince(0);

        $this->assertEquals(
            new Changes(
                array(
                    'type'    => 'category',
                    'since'   => 0,
                    'count'   => 1,
                    'changes' => array(
                        new Change(
                            array(
                                'sequence' => 1,
                                'id'       => 42,
                                'data'     => new Category(
                                    array(
                                        'id'   => 42,
                                        'OXID' => 42,
                                    )
                                ),
                            )
                        ),
                    ),
                )
            ),
            $changes
        );
    }

    public function testRunModifierLoadProduct()
    {
        $databaseMock = $this->getMock(DatabaseInterface::class, ['query'], [], '', false);
        $modifierMock = $this->getMock(Modifier::class);

        $repository = new CategoryRepository($databaseMock, [$modifierMock]);

        $databaseMock
            ->expects($this->once())
            ->method('query')
            ->will($this->returnValue([['id' => 42, 'sequence' => 1, 'OXID' => 42]]));

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

    public function testSetDeletedMarker()
    {
        $databaseMock = $this->getMock(DatabaseInterface::class, ['query'], [], '', false);
        $repository = new CategoryRepository($databaseMock);

        $databaseMock
            ->expects($this->exactly(2))
            ->method('query')
            ->willReturnOnConsecutiveCalls(
                $this->returnValue([['id' => 42, 'sequence' => 1, 'OXID' => null]]),
                $this->returnValue([['OXID' => 42, 'TYPE' => 'variant']])
            );

        $changes = $repository->getChangesSince(0);

        $this->assertEquals(true, $changes->changes[0]->deleted);
        $this->assertEquals(null, $changes->changes[0]->data);
    }
}
