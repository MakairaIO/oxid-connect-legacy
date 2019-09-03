<?php

namespace Makaira\Connect\Repository;


use Makaira\Connect\Change;
use Makaira\Connect\DatabaseInterface;
use Makaira\Connect\Result\Changes;
use Makaira\Connect\Type\Category\Category;
use Makaira\Connect\Modifier;
use Makaira\Connect\Repository\ModifierList;

class CategoryRepositoryTest extends \PHPUnit_Framework_TestCase
{
    public function testLoadCategory()
    {
        $databaseMock = $this->getMock(DatabaseInterface::class);
        $modifiersMock = $this->getMock(ModifierList::class);
        $repository = new CategoryRepository($databaseMock, $modifiersMock);

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
            new Change(
                'id' => 42,
                'type' => 'category',
                array(
                    'data' => new Category(
                        array(
                            'id' => 42,
                        )
                    ),
                )
            ),
            $change
        );
    }

    public function testSetDeletedMarker()
    {
        $databaseMock = $this->getMock(DatabaseInterface::class);
        $modifiersMock = $this->getMock(ModifierList::class);
        $repository = new CategoryRepository($databaseMock, $modifiersMock);

        $databaseMock
            ->expects($this->once())
            ->method('query')
            ->will($this->returnValue([]));

        $modifiersMock
            ->expects($this->never())
            ->method('applyModifiers');

        $change = $repository->get(42);
        $this->assertEquals(
            new Change(
                'id' => 42,
                'type' => 'category',
                array(
                    'deleted' => true,
                )
            ),
            $change
        );
    }

    public function testRunModifierLoadCategory()
    {
        $databaseMock = $this->getMock(DatabaseInterface::class);
        $modifiersMock = $this->getMock(ModifierList::class);
        $repository = new CategoryRepository($databaseMock, $modifiersMock);

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
            new Change(
                'id' => 42,
                'type' => 'category',
                array(
                    'data' => 'modified',
                )
            ),
            $change
        );
    }

    public function testGetAllIds()
    {
        $databaseMock = $this->getMock(DatabaseInterface::class);
        $modifiersMock = $this->getMock(ModifierList::class);
        $repository = new CategoryRepository($databaseMock, $modifiersMock);

        $databaseMock
            ->expects($this->once())
            ->method('query')
            ->will($this->returnValue([['OXID' => 42]]));

        $this->assertEquals([42], $repository->getAllIds());
    }
}
