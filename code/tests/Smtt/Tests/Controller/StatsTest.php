<?php

namespace Smtt\Tests\Controller;

use Smtt\Controller\Stats;
use Smtt\dto\MoDates;
use Smtt\Exception\QueryException;
use Smtt\Service\Statistics;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class StatsTest extends \PHPUnit_Framework_TestCase
{

    public function testStats()
    {
        $moDates = new MoDates();
        $moDates->minDate = new \DateTime('2010-02-14 13:11:23');
        $moDates->maxDate = new \DateTime('2015-12-15 19:12:11');

        $serviceMock = $this->getMockBuilder(Statistics::class)->disableOriginalConstructor()->getMock();
        $serviceMock->expects($this->once())->method('getMoCountLast15m')->willReturn(985);
        $serviceMock->expects($this->once())->method('getDates4LastMo')->willReturn($moDates);

        $request = new Request();

        $controller = new Stats($serviceMock);
        /** @var JsonResponse $response */
        $response = $controller($request);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('last_15_min_mo_count', $data);
        $this->assertEquals(985, $data['last_15_min_mo_count']);
        $this->assertArrayHasKey('time_span_last_10k', $data);
        $this->assertCount(2, $data['time_span_last_10k']);
        $this->assertEquals($moDates->minDate->format('Y-m-d H:i:s'), $data['time_span_last_10k'][0]);
        $this->assertEquals($moDates->maxDate->format('Y-m-d H:i:s'), $data['time_span_last_10k'][1]);
    }

    public function testException()
    {
        $serviceMock = $this->getMockBuilder(Statistics::class)->disableOriginalConstructor()->getMock();
        $serviceMock->expects($this->once())
            ->method('getMoCountLast15m')->willThrowException(new QueryException('Test exception'));

        $request = new Request();

        $controller = new Stats($serviceMock);
        /** @var JsonResponse $response */
        $response = $controller($request);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(500, $response->getStatusCode());
    }
}
