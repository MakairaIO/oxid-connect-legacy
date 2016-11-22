<?php

namespace Makaira\Connect\Repository;


use Makaira\Connect\Change;
use Makaira\Connect\DatabaseInterface;
use Makaira\Connect\Type\Variant\Variant;

class VariantRepositoryTest extends \PHPUnit_Framework_TestCase
{
    public function testLoadVariant()
    {
        $databaseMock = $this->getMock(DatabaseInterface::class);
        $modifiersMock = $this->getMock(ModifierList::class);
        $repository = new VariantRepository($databaseMock, $modifiersMock);

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
                    'data' => new Variant(
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
        $repository = new VariantRepository($databaseMock, $modifiersMock);

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
                    'deleted' => true,
                )
            ),
            $change
        );
    }

    public function testRunModifierLoadVariant()
    {
        $databaseMock = $this->getMock(DatabaseInterface::class);
        $modifiersMock = $this->getMock(ModifierList::class);
        $repository = new VariantRepository($databaseMock, $modifiersMock);

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
                    'data' => 'modified',
                )
            ),
            $change
        );
    }
}
