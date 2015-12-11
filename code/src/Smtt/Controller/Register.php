<?php

namespace Smtt\Controller;

use Smtt\RegisterMo\RegisterMoInterface;
use Smtt\RequestProcessor;
use Smtt\Traits\Logger;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Register
{
    use Logger;

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
     * Action processing mo requests
     * @param Request $request
     * @return Response
     */
    public function __invoke(Request $request)
    {
        $response = new JsonResponse();
        try {
            $moRequest = $this->requestProcessor->process($request);
            $this->registerMo->register($moRequest);
            $response->setData(['status' => 'ok']);
        } catch (\Smtt\Exception\Exception $e) {
            $response->setStatusCode($e->getCode());
            $response->setData([
                'status'    => 'error',
                'message'   => $e->getMessage(),
            ]);
        } catch (\Exception $e) { //Unexpected exception
            $this->logger->error('Unexpected exception caught', [
                'exception' => $e->__toString(),
            ]);
            $response->setStatusCode(500);
        }
        return $response;
    }
}
