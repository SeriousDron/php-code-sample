<?php

namespace Smtt\Tests;

use Smtt\Exception\NotEnoughParametersException;
use Smtt\Exception\UnexpectedValueException;
use Smtt\MoRequestFactory;
use Symfony\Component\HttpFoundation\Request;

class MoRequestFactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var MoRequestFactory */
    protected $moRequestFactory;

    protected function setUp()
    {
        parent::setUp();
        $this->moRequestFactory = new MoRequestFactory();
    }

    public function testGetPostParams()
    {
        $data = array(
            'msisdn'        => '60123456789',
            'operatorid'    => 3,
            'shortcodeid'   => 8,
            'text'          => 'ON GAMES',
        );

        $requests = [new Request($data), new Request([], $data)];
        foreach ($requests as $request) {
            $moRequest = $this->moRequestFactory->createFromRequest($request);

            $this->assertInternalType('string', $moRequest->msisdn);
            $this->assertEquals($data['msisdn'], $moRequest->msisdn);

            $this->assertInternalType('int', $moRequest->operatorid);
            $this->assertEquals($data['operatorid'], $moRequest->operatorid);

            $this->assertInternalType('int', $moRequest->shortcodeid);
            $this->assertEquals($data['shortcodeid'], $moRequest->shortcodeid);

            $this->assertInternalType('string', $moRequest->text);
            $this->assertEquals($data['text'], $moRequest->text);
        }
    }

    public function testEmptyString()
    {
        $data = array(
            'msisdn'        => '',
            'operatorid'    => 365,
            'shortcodeid'   => 112,
            'text'          => 'ON LEARNING',
        );

        $this->setExpectedException(UnexpectedValueException::class);
        $this->moRequestFactory->createFromRequest(new Request($data));
    }

    public function testNonNumericOperatorId()
    {
        $data = array(
            'msisdn'        => '23458684',
            'operatorid'    => 'operatorid',
            'shortcodeid'   => 112,
            'text'          => 'ON LEARNING',
        );

        $this->setExpectedException(UnexpectedValueException::class);
        $this->moRequestFactory->createFromRequest(new Request($data));
    }

    public function testNonNumericShortCodeIs()
    {
        $data = array(
            'msisdn'        => 23458684,
            'operatorid'    => 6872,
            'shortcodeid'   => 'NotNumeric',
            'text'          => 'ON GAMING',
        );

        $this->setExpectedException(UnexpectedValueException::class);
        $this->moRequestFactory->createFromRequest(new Request($data));
    }

    /**
     * @expectedException UnexpectedValueException
     */
    public function testSignedInt()
    {
        $data = array(
            'msisdn'        => '-548864',
            'operatorid'    => 9984,
            'shortcodeid'   => -112,
            'text'          => 'ON LEARNING',
        );

        $this->setExpectedException(UnexpectedValueException::class);
        $this->moRequestFactory->createFromRequest(new Request($data));
    }

    public function testNoParameter()
    {
        $data = array(
            'shortcodeid'   => 112,
            'text'          => 'ON GAMING',
        );

        $this->setExpectedException(NotEnoughParametersException::class);
        $this->moRequestFactory->createFromRequest(new Request($data));
    }
}
