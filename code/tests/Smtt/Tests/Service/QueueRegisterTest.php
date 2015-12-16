<?php

namespace Smtt\Tests\Service;

use Psr\Log\LoggerInterface;
use Smtt\dto\MoRequest;
use Smtt\dto\RegisterResult;
use Smtt\Exception\ProcessingException;
use Smtt\Queue\GearmanQueue;
use Smtt\Service\QueueRegister;

class QueueRegisterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function testSuccessfulProcessing()
    {
        $moRequest = unserialize('O:18:"Smtt\dto\MoRequest":4:{s:6:"msisdn";s:11:"60123456789";'
            . 's:10:"operatorid";i:3;s:11:"shortcodeid";i:8;s:4:"text";s:8:"ON GAMES";}');

        $queue = $this->getMockBuilder(GearmanQueue::class)->disableOriginalConstructor()->getMock();
        $queue->expects($this->once())
            ->method('rpc')->with('test_queue', $moRequest)->willReturn(RegisterResult::success());

        $queueRegister = new QueueRegister($queue);
        $queueRegister->setQueue('test_queue');

        $result = $queueRegister->register($moRequest);
        $this->assertInstanceOf(RegisterResult::class, $result);
        $this->assertTrue($result->successful);
    }

    public function testFailedProcessing()
    {
        $moRequest = new MoRequest();

        $queue = $this->getMockBuilder(GearmanQueue::class)->disableOriginalConstructor()->getMock();
        $queue->expects($this->any())->method('rpc')->willReturn(false);

        $queueRegister = new QueueRegister($queue);
        $result = $queueRegister->register($moRequest);
        $this->assertInstanceOf(RegisterResult::class, $result);
        $this->assertFalse($result->successful);
    }

    public function testProcessingException()
    {
        $moRequest = new MoRequest();

        $queue = $this->getMockBuilder(GearmanQueue::class)->disableOriginalConstructor()->getMock();
        $queue->expects($this->any())->method('rpc')->willThrowException(new ProcessingException('Test exception'));

        $logger = $this->getMockBuilder(LoggerInterface::class)->disableOriginalConstructor()->getMock();
        $logger->expects($this->once())
            ->method('error');

        $queueRegister = new QueueRegister($queue);
        $queueRegister->setLogger($logger);
        $result = $queueRegister->register($moRequest);
        $this->assertInstanceOf(RegisterResult::class, $result);
        $this->assertFalse($result->successful);
    }
}
