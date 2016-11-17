<?php

namespace Makaira\Connect;

class PdoDatabaseTest extends \PHPUnit_Framework_TestCase
{

    public function testDsn()
    {
        $this->assertEquals('mysql:host=HOST;dbname=DBNAME', PdoDatabase::buildMySqlDsn('HOST', 'DBNAME', false));
        $this->assertEquals('mysql:host=HOST;dbname=DBNAME;port=90', PdoDatabase::buildMySqlDsn('HOST:90', 'DBNAME', false));
        $this->assertEquals('mysql:host=HOST;dbname=DBNAME;charset=utf8', PdoDatabase::buildMySqlDsn('HOST', 'DBNAME', true));
        $this->assertEquals('mysql:host=HOST;dbname=DBNAME;port=90;charset=utf8', PdoDatabase::buildMySqlDsn('HOST:90', 'DBNAME', true));
    }

    public function testQuery()
    {
        $stmtMock = $this->getMock(\PDOStatement::class);
        $stmtMock
            ->expects($this->once())
            ->method('execute')
            ->will($this->returnValue(true));
        $stmtMock
            ->expects($this->once())
            ->method('fetchAll')
            ->with(\PDO::FETCH_ASSOC)
            ->will($this->returnValue('foo'));

        $pdoMock = $this->getMock(\PDO::class, ['prepare'], [], '', false);
        $pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('test')
            ->will($this->returnValue($stmtMock));

        $db = new PdoDatabase($pdoMock);
        $this->assertEquals('foo', $db->query('test'));
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionCode 42
     * @expectedExceptionMessage Foo
     */
    public function testQueryFailure()
    {
        $stmtMock = $this->getMock(\PDOStatement::class);
        $stmtMock
            ->expects($this->once())
            ->method('execute')
            ->will($this->returnValue(false));
        $stmtMock
            ->expects($this->once())
            ->method('errorInfo')
            ->will($this->returnValue([0, 42, 'Foo']));

        $pdoMock = $this->getMock(\PDO::class, ['prepare'], [], '', false);
        $pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('test')
            ->will($this->returnValue($stmtMock));

        $db = new PdoDatabase($pdoMock);
        $this->assertEquals('foo', $db->query('test'));
    }

    public function testQueryWithParameters()
    {
        $stmtMock = $this->getMock(\PDOStatement::class);
        $stmtMock
            ->expects($this->once())
            ->method('execute')
            ->will($this->returnValue(true));
        $stmtMock
            ->expects($this->once())
            ->method('fetchAll')
            ->with(\PDO::FETCH_ASSOC)
            ->will($this->returnValue('foo'));
        $stmtMock
            ->expects($this->exactly(3))
            ->method('bindValue')
            ->with(
                $this->logicalOr(':foo', ':alice', ':bob'),
                $this->logicalOr('bar', null, 42),
                $this->logicalOr(\PDO::PARAM_STR, \PDO::PARAM_NULL, \PDO::PARAM_INT)
            );

        $pdoMock = $this->getMock(\PDO::class, ['prepare'], [], '', false);
        $pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('test')
            ->will($this->returnValue($stmtMock));

        $db = new PdoDatabase($pdoMock);
        $this->assertEquals('foo', $db->query('test', ['foo' => 'bar', 'alice' => null, 'bob' => 42]));
    }

}
