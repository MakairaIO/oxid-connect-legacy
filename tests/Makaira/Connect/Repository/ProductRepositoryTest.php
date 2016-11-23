<?php

namespace Makaira\Connect\Repository;

use Makaira\Connect\Change;
use Makaira\Connect\Modifier;
use Makaira\Connect\Type\Product\Product;
use Makaira\Connect\DatabaseInterface;
use Makaira\Connect\Result\Changes;

class ProductRepositoryTest extends \PHPUnit_Framework_TestCase
{
    public function testLoadProduct()
    {
        $databaseMock = $this->getMock(DatabaseInterface::class);
        $modifiersMock = $this->getMock(ModifierList::class);
        $repository = new ProductRepository($databaseMock, $modifiersMock);

        $databaseMock
            ->expects($this->once())
            ->method('query')
            ->will($this->returnValue([['id' => 42]]));

        $modifiersMock
            ->expects($this->any())
            ->method('applyModifiers')
            ->will($this->returnArgument(0));

        $change = $repository->get(42);
        $this->assertEquals(
            new Change(array(
                'data' => new Product(array(
                    'id' => 42,
                )),
            )),
            $change
        );
    }

    public function testSetDeletedMarker()
    {
        $databaseMock = $this->getMock(DatabaseInterface::class);
        $modifiersMock = $this->getMock(ModifierList::class);
        $repository = new ProductRepository($databaseMock, $modifiersMock);

        $databaseMock
            ->expects($this->once())
            ->method('query')
            ->will($this->returnValue([]));

        $modifiersMock
            ->expects($this->never())
            ->method('applyModifiers');

        $change = $repository->get(42);
        $this->assertEquals(
            new Change(array(
                'deleted' => true,
            )),
            $change
        );
    }

    public function testRunModifierLoadProduct()
    {
        $databaseMock = $this->getMock(DatabaseInterface::class);
        $modifiersMock = $this->getMock(ModifierList::class);
        $repository = new ProductRepository($databaseMock, $modifiersMock);

        $databaseMock
            ->expects($this->once())
            ->method('query')
            ->will($this->returnValue([['id' => 42]]));

        $modifiersMock
            ->expects($this->once())
            ->method('applyModifiers')
            ->will($this->returnValue('modified'));

        $change = $repository->get(42);
        $this->assertEquals(
            new Change(array(
                'data' => 'modified',
            )),
            $change
        );
    }

    public function testGetAllIds()
    {
        $databaseMock = $this->getMock(DatabaseInterface::class);
        $modifiersMock = $this->getMock(ModifierList::class);
        $repository = new ProductRepository($databaseMock, $modifiersMock);

        $databaseMock
            ->expects($this->once())
            ->method('query')
            ->will($this->returnValue([['OXID' => 42]]));

        $this->assertEquals([42], $repository->getAllIds());
    }
}
