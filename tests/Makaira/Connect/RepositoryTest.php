<?php

namespace Makaira\Connect;

use Makaira\Connect\Result\Changes;

class RepositoryTest extends \PHPUnit_Framework_TestCase
{
    public function testGetChangesForEmptyResult()
    {
        $databaseMock = $this->getMock(DatabaseInterface::class);
        $repository = new Repository($databaseMock);

        $databaseMock
            ->expects($this->any())
            ->method('query')
            ->will($this->returnValue([]));

        $result = $repository->getChangesSince(0, 50);

        $this->assertEquals(
            new Changes([
                'since' => 0,
                'count' => 0,
                'changes' => [],
            ]),
            $result
        );
    }

    public function testGetChangesForSingleResult()
    {
        $databaseMock = $this->getMock(DatabaseInterface::class);
        $repositoryMock = $this->getMock(RepositoryInterface::class);
        $repository = new Repository($databaseMock, ['product' => $repositoryMock]);

        $databaseMock
            ->expects($this->any())
            ->method('query')
            ->will($this->returnValue([
                ['id' => 42, 'sequence' => 1, 'type' => 'product']
            ]));

        $repositoryMock
            ->expects($this->any())
            ->method('get')
            ->with(42)
            ->will($this->returnValue(new Change(['data' => 'product-42'])));

        $result = $repository->getChangesSince(0, 50);

        $this->assertEquals(
            new Changes([
                'since' => 0,
                'count' => 1,
                'changes' => [
                    new Change([
                        'id' => 42,
                        'sequence' => 1,
                        'data' => 'product-42',
                        'type' => 'product',
                    ])
                ],
            ]),
            $result
        );
    }

    /**
     * @expectedException \OutOfBoundsException
     */
    public function testGetChangesFromInvalidRepository()
    {
        $databaseMock = $this->getMock(DatabaseInterface::class);
        $repository = new Repository($databaseMock, []);

        $databaseMock
            ->expects($this->any())
            ->method('query')
            ->will($this->returnValue([
                ['id' => 42, 'sequence' => 1, 'type' => 'unknown']
            ]));

        $repository->getChangesSince(0, 50);
    }

    public function testGetChangesForMultipleResults()
    {
        $databaseMock = $this->getMock(DatabaseInterface::class);
        $repositoryMock = $this->getMock(RepositoryInterface::class);
        $repository = new Repository($databaseMock, [
            'firstRepo' => $repositoryMock,
            'secondRepo' => $repositoryMock,
        ]);

        $databaseMock
            ->expects($this->any())
            ->method('query')
            ->will($this->returnValue([
                ['id' => 42, 'sequence' => 1, 'type' => 'firstRepo'],
                ['id' => 43, 'sequence' => 2, 'type' => 'secondRepo'],
                ['id' => 44, 'sequence' => 3, 'type' => 'firstRepo'],
            ]));

        $repositoryMock
            ->expects($this->any())
            ->method('get')
            ->will($this->returnCallback(function () {
                return new Change(['data' => 'data']);
            }));

        $result = $repository->getChangesSince(0, 50);

        $this->assertEquals(
            new Changes([
                'since' => 0,
                'count' => 3,
                'changes' => [
                    // This order MUST NOT change
                    new Change([
                        'id' => 42,
                        'sequence' => 1,
                        'data' => 'data',
                        'type' => 'firstRepo',
                    ]),
                    new Change([
                        'id' => 43,
                        'sequence' => 2,
                        'data' => 'data',
                        'type' => 'secondRepo',
                    ]),
                    new Change([
                        'id' => 44,
                        'sequence' => 3,
                        'data' => 'data',
                        'type' => 'firstRepo',
                    ]),
                ],
            ]),
            $result
        );
    }

    public function testTouchExecutesQuery()
    {
        $databaseMock = $this->getMock(DatabaseInterface::class);
        $repository = new Repository($databaseMock);

        $databaseMock
            ->expects($this->any())
            ->method('execute')
            ->withConsecutive($this->stringContains('REPLACE INTO'), ['type' => 'product', 'id' => 42]);

        $repository->touch('product', 42);
    }

    public function testTouchAllOneRepository()
    {
        $databaseMock = $this->getMock(DatabaseInterface::class);
        $databaseMock
            ->expects($this->exactly(3))
            ->method('execute')
            ->withConsecutive(
                [$this->stringContains('REPLACE INTO'), ['type' => 'firstRepo', 'id' => 1]],
                [$this->stringContains('REPLACE INTO'), ['type' => 'firstRepo', 'id' => 2]],
                [$this->stringContains('REPLACE INTO'), ['type' => 'firstRepo', 'id' => 3]]
                );
        $repositoryMock1 = $this->getMock(RepositoryInterface::class);
        $repositoryMock1
            ->expects($this->once())
            ->method('getAllIds')
            ->will($this->returnValue([1,2,3]));
        $repository = new Repository($databaseMock, [
            'firstRepo' => $repositoryMock1,
        ]);
        $repository->touchAll();
    }

    public function testTouchAllMultipleRepositories()
    {
        $databaseMock = $this->getMock(DatabaseInterface::class);
        $databaseMock
            ->expects($this->exactly(4))
            ->method('execute')
            ->withConsecutive(
                [$this->stringContains('REPLACE INTO'), ['type' => 'firstRepo', 'id' => 1]],
                [$this->stringContains('REPLACE INTO'), ['type' => 'firstRepo', 'id' => 2]],
                [$this->stringContains('REPLACE INTO'), ['type' => 'firstRepo', 'id' => 3]],
                [$this->stringContains('REPLACE INTO'), ['type' => 'secondRepo', 'id' => 4]]
            );
        $repositoryMock1 = $this->getMock(RepositoryInterface::class);
        $repositoryMock1
            ->expects($this->once())
            ->method('getAllIds')
            ->will($this->returnValue([1,2,3]));
        $repositoryMock2 = $this->getMock(RepositoryInterface::class);
        $repositoryMock2
            ->expects($this->once())
            ->method('getAllIds')
            ->will($this->returnValue([4]));
        $repository = new Repository($databaseMock, [
            'firstRepo' => $repositoryMock1,
            'secondRepo' => $repositoryMock2,
        ]);
        $repository->touchAll();
    }
}
