<?php

namespace Smtt\Queue;

use Smtt\Exception\ProcessingException;

interface QueueInterface
{
    /**
     * Put task in queue and synchronously wait for result
     *
     * @param string $queue
     * @param string $workload
     * @return string
     * @throws ProcessingException
     */
    public function rpc($queue, $workload);
}
