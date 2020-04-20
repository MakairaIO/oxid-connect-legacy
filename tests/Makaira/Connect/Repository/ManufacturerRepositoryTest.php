<?php

namespace Makaira\Connect\Repository;

use Makaira\Connect\Change;
use Makaira\Connect\DatabaseInterface;
use Makaira\Connect\Result\Changes;
use Makaira\Connect\Type\Manufacturer\Manufacturer;
use Makaira\Connect\Modifier;
use Makaira\Connect\Repository\ModifierList;

class ManufacturerRepositoryTest extends \PHPUnit_Framework_TestCase
{
    public function testLoadManufacturer()
    {
        $databaseMock = $this->getMock(DatabaseInterface::class);
        $modifiersMock = $this->getMock(ModifierList::class, [], [], '', false);
        $repository = new ManufacturerRepository($databaseMock, $modifiersMock);

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
                array(
                    'id' => 42,
                    'type' => 'manufacturer',
                    'data' => new Manufacturer(
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
        $modifiersMock = $this->getMock(ModifierList::class, [], [], '', false);
        $repository = new ManufacturerRepository($databaseMock, $modifiersMock);

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
                array(
                    'id' => 42,
                    'type' => 'manufacturer',
                    'deleted' => true,
                )
            ),
            $change
        );
    }

    public function testRunModifierLoadManufacturer()
    {
        $databaseMock = $this->getMock(DatabaseInterface::class);
        $modifiersMock = $this->getMock(ModifierList::class, [], [], '', false);
        $repository = new ManufacturerRepository($databaseMock, $modifiersMock);

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
                array(
                    'id' => 42,
                    'type' => 'manufacturer',
                    'data' => 'modified',
                )
            ),
            $change
        );
    }

    public function testGetAllIds()
    {
        $databaseMock = $this->getMock(DatabaseInterface::class);
        $modifiersMock = $this->getMock(ModifierList::class, [], [], '', false);
        $repository = new ManufacturerRepository($databaseMock, $modifiersMock);

        $databaseMock
            ->expects($this->once())
            ->method('query')
            ->will($this->returnValue([['OXID' => 42]]));

        $this->assertEquals([42], $repository->getAllIds());
    }
}
