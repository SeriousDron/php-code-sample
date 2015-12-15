<?php

namespace Smtt\Tests\Controller;

use Psr\Log\LoggerInterface;
use Smtt\Controller\Register;
use Smtt\Service\InstantRegister;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class RegisterTest extends \PHPUnit_Framework_TestCase
{
    public function testNormalProcessing()
    {
        $registerMo = $this->buildSuccessRegisterMock();

        $request = new Request(array(
            'msisdn'        => '60123456789',
            'operatorid'    => 3,
            'shortcodeid'   => 8,
            'text'          => 'ON GAMES',
        ));

        $controller = new Register($registerMo);
        /** @var JsonResponse $response */
        $response = $controller($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('{"status":"ok"}', $response->getContent());
    }

    public function testException()
    {
        $registerMo = $this->getMockBuilder(InstantRegister::class)
            ->disableOriginalConstructor()->getMock();
        $registerMo->expects($this->once())
            ->method('register')
            ->willThrowException(new \Exception('Test'));

        $request = new Request(array(
            'msisdn'        => '351535138',
            'operatorid'    => 651,
            'shortcodeid'   => 23,
            'text'          => 'ON EDUCATION',
        ));

        $logger = $this->getMockBuilder(LoggerInterface::class)->disableOriginalConstructor()->getMock();
        $logger->expects($this->once())
            ->method('error');

        $controller = new Register($registerMo);
        $controller->setLogger($logger);

        /** @var JsonResponse $response */
        $response = $controller($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(500, $response->getStatusCode());
    }

    public function testBadRequest()
    {
        $registerMo = $this->buildSuccessRegisterMock();

        $controller = new Register($registerMo);
        /** @var JsonResponse $response */
        $response = $controller(new Request());

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(400, $response->getStatusCode());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function buildSuccessRegisterMock()
    {
        $registerMo = $this->getMockBuilder(InstantRegister::class)
            ->disableOriginalConstructor()->getMock();
        $registerMo->expects($this->any())
            ->method('register')
            ->willReturn(true);
        return $registerMo;
    }
}
