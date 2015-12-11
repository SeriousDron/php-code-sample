<?php

namespace Smtt\Service;

use Smtt\dto\MoRequest;
use Smtt\Exception\ProcessingException;
use Smtt\Queue\QueueInterface;
use Smtt\Traits\Logger;

class QueueRegister implements RegisterMoInterface
{
    use Logger;

    protected $queue = 'smtt_register_mo';

    /** @var  QueueInterface */
    protected $queueServer;

    /**
     * QueueRegister constructor.
     * @param QueueInterface $queueServer
     */
    public function __construct(QueueInterface $queueServer)
    {
        $this->queueServer = $queueServer;
    }

    /**
     * Set queue name
     *
     * @param string $queue
     */
    public function setQueue($queue)
    {
        $this->queue = $queue;
    }

    /**
     * @param MoRequest $moRequest
     * @return boolean
     */
    public function register(MoRequest $moRequest)
    {
        try {
            $jsonResult = $this->queueServer->rpc($this->queue, json_encode($moRequest));
            $result = json_decode($jsonResult);
            if (!is_bool($result)) {
                throw new ProcessingException('Unexpected result type');
            }
            return $result;
        } catch (ProcessingException $e) {
            $this->logger->error('Unexpected exception processing task', ['exception' => $e]);
            return false;
        }
    }
}
