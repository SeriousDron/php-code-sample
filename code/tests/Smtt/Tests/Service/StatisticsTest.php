<?php

namespace Smtt\Tests\Service;

use Doctrine\DBAL\Connection;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Smtt\Exception\QueryException;
use Doctrine\DBAL\Statement;
use Smtt\Exception\UnexpectedValueException;
use Smtt\Service\Statistics;

class StatisticsTest extends \PHPUnit_Framework_TestCase
{
    public function testMoCount()
    {
        $count = 8946;

        $stmt = $this->getMockBuilder(Statement::class)->disableOriginalConstructor()->getMock();
        $stmt->expects($this->once())->method('fetch')->willReturn(['count' => $count]);

        $dbMock = $this->getMockBuilder(Connection::class)->disableOriginalConstructor()->getMock();
        $dbMock->expects($this->once())->method('query')->willReturn($stmt);

        $statistics = new Statistics($dbMock);
        $result = $statistics->getMoCountLast15m();
        $this->assertEquals($count, $result);
    }

    public function testMoCountException()
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)->disableOriginalConstructor()->getMock();
        $logger->expects($this->once())
            ->method('error');

        $dbMock = $this->getMockBuilder(Connection::class)->disableOriginalConstructor()->getMock();
        $dbMock->expects($this->once())->method('query')->willThrowException(new \Doctrine\DBAL\DBALException());

        $statistics = new Statistics($dbMock);
        $statistics->setLogger($logger);
        $this->setExpectedException(QueryException::class);
        $statistics->getMoCountLast15m();
    }

    public function testDates()
    {
        $stmt = $this->getMockBuilder(Statement::class)->disableOriginalConstructor()->getMock();
        $stmt->expects($this->once())->method('fetch')->willReturn(['minDate' => '2011-06-17 21:45:07', 'maxDate' => '2015-12-13 04:37:51']);

        $dbMock = $this->getMockBuilder(Connection::class)->disableOriginalConstructor()->getMock();
        $dbMock->expects($this->once())->method('prepare')->willReturn($stmt);

        $statistics = new Statistics($dbMock);
        $result = $statistics->getDates4LastMo();
        $this->assertInstanceOf(\Smtt\dto\MoDates::class, $result);
        $this->assertEquals(new \DateTime('2011-06-17 21:45:07'), $result->minDate);
        $this->assertEquals(new \DateTime('2015-12-13 04:37:51'), $result->maxDate);
    }

    public function testDatesBadCount()
    {
        $dbMock = $this->getMockBuilder(Connection::class)->disableOriginalConstructor()->getMock();

        $statistics = new Statistics($dbMock);
        $this->setExpectedException(UnexpectedValueException::class);
        $statistics->getDates4LastMo(-1);
    }

    public function testDatesException()
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)->disableOriginalConstructor()->getMock();
        $logger->expects($this->once())->method('error');

        $dbMock = $this->getMockBuilder(Connection::class)->disableOriginalConstructor()->getMock();
        $dbMock->expects($this->once())->method('prepare')->willThrowException(new \Doctrine\DBAL\DBALException());

        $statistics = new Statistics($dbMock);
        $statistics->setLogger($logger);
        $this->setExpectedException(QueryException::class);
        $statistics->getDates4LastMo(1000);
    }
}
