<?php

namespace Smtt\Tests\Service;

use Doctrine\DBAL\Connection;
use Smtt\dto\MoRequest;
use Smtt\Service\CommandRunner;
use Smtt\Service\InstantRegister;

class InstantRegisterTest extends \PHPUnit_Framework_TestCase
{

    public function testCorrectRequest()
    {
        $moRequest = unserialize('O:18:"Smtt\dto\MoRequest":4:{s:6:"msisdn";s:11:"60123456789";'
            . 's:10:"operatorid";i:3;s:11:"shortcodeid";i:8;s:4:"text";s:8:"ON GAMES";}');

        $dbMock = $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->getMock();
        $dbMock->expects($this->once())
            ->method('insert')
            ->willReturn(1);

        $runner = $this->getMock(CommandRunner::class);
        $runner->method('run')->willReturn('xc6u9nAbGf6A_ccBifF_17jt');

        $instantRegister = new InstantRegister($runner, $dbMock);
        $result = $instantRegister->register($moRequest);
        $this->assertTrue($result);
    }

    public function testBadHash()
    {
        $moRequest = new MoRequest();

        $dbMock = $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->getMock();
        $dbMock->expects($this->never())
            ->method('insert');

        $runner = $this->getMock(CommandRunner::class);
        $runner->method('run')->willReturn('');
        $instantRegister = new InstantRegister($runner, $dbMock);
        $result = $instantRegister->register($moRequest);
        $this->assertFalse($result);
    }
}
