<?php
namespace Smtt\Queue;

use Smtt\Exception\ProcessingException;

class GearmanQueue implements QueueInterface
{
    protected $client;

    /**
     * GearmanQueue constructor.
     * @param $client
     */
    public function __construct()
    {
        $this->client = new \GearmanClient();
    }

    /**
     * Put task in queue and synchronously wait for result
     *
     * @param string $queue
     * @param string $workload
     * @return string undencoded worker call result
     * @throws ProcessingException
     */
    public function rpc($queue, $workload)
    {
        $result = $this->client->doNormal($queue, $workload);
        $returnCode = $this->client->returnCode();
        if ($returnCode !== GEARMAN_SUCCESS) {
            throw new ProcessingException("Queue processing returned with result: {$result}", $returnCode);
        }
        return $result;
    }
}
