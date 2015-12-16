<?php

namespace Smtt\Tests\Controller;

use Psr\Log\LoggerInterface;
use Smtt\Controller\Register;
use Smtt\dto\RegisterResult;
use Smtt\Service\RegisterMoInterface;
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
        $registerMo = $this->getMock(RegisterMoInterface::class);
        $registerMo->expects($this->once())
            ->method('register')
            ->willThrowException(new \Exception('Test'));

        $request = new Request(array(
            'msisdn'        => '351535138',
            'operatorid'    => 651,
            'shortcodeid'   => 23,
            'text'          => 'ON EDUCATION',
        ));

        $this->expectFailure($registerMo, $request);
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function testFailedProcessing()
    {
        $registerMo = $this->getMock(RegisterMoInterface::class);
        $registerMo->expects($this->once())
            ->method('register')
            ->willReturn(RegisterResult::fail('Test failure'));

        $request = new Request(array(
            'msisdn'        => 992623,
            'operatorid'    => 1,
            'shortcodeid'   => 99999,
            'text'          => 'ON TEST',
        ));

        $this->expectFailure($registerMo, $request);
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
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function buildSuccessRegisterMock()
    {
        $registerMo = $this->getMock(RegisterMoInterface::class);
        $registerMo->expects($this->any())
            ->method('register')
            ->willReturn(RegisterResult::success());
        return $registerMo;
    }

    /**
     * Common method to check all necessary failure actions
     *
     * @param $registerMo
     * @param $request
     */
    protected function expectFailure($registerMo, $request)
    {
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
}
