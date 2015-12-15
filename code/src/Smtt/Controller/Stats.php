<?php

namespace Smtt\Controller;

use Smtt\Service\Statistics;
use Smtt\Traits\Logger;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class Stats
{
    use Logger;

    /** @var  Statistics */
    protected $statsService;

    /**
     * Stats constructor.
     * @param Statistics $statsService
     */
    public function __construct(Statistics $statsService)
    {
        $this->statsService = $statsService;
    }

    /**
     * @param Request $request
     * return JsonResponse
     */
    public function __invoke(Request $request)
    {
        $response = new JsonResponse();
        try {
            $data = array();
            $data['last_15_min_mo_count'] = $this->statsService->getMoCountLast15m();
            $moDate = $this->statsService->getDates4LastMo();
            $data['time_span_last_10k'] = array(
                $moDate->minDate->format('Y-m-d H:i:s'),
                $moDate->maxDate->format('Y-m-d H:i:s'),
            );
            $response->setData($data);
            $response->setStatusCode(200);
            return $response;
        } catch (\Smtt\Exception\Exception $e) {
            $response->setStatusCode($e->getCode());
            $response->setData([
                'status'    => 'error',
                'message'   => $e->getMessage(),
            ]);
            return $response;
        }
    }
}
