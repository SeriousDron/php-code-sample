<?php

namespace Smtt\Controller;

use Smtt\Exception\Exception;
use Smtt\RegisterMoInterface;
use Smtt\RequestProcessor;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Register
{
    /** @var RegisterMoInterface */
    protected $registerMo;

    /** @var RequestProcessor */
    protected $requestProcessor;

    /**
     * Register contoller constructor.
     * @param RegisterMoInterface $registerMo Strategy of RegisterMo processing
     */
    public function __construct(RegisterMoInterface $registerMo)
    {
        $this->registerMo = $registerMo;
        $this->requestProcessor = new RequestProcessor();
    }

    /**
     * Action processing all mo requests
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $response = new JsonResponse();
        try {
            $moRequest = $this->requestProcessor->process($request);
            $this->registerMo->register($moRequest);
        } catch (Exception $e) {
            $response->setStatusCode($e->getCode());
            $response->setData([
                'status'    => 'error',
                'message'   => $e->getMessage(),
            ]);
        }
    }
}
