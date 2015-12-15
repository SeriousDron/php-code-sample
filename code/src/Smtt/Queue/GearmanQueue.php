<?php
namespace Smtt\Queue;

use Smtt\Exception\ProcessingException;

class GearmanQueue implements QueueInterface
{
    /** @var \GearmanClient */
    protected $client;

    /**
     * GearmanQueue constructor.
     */
    public function __construct()
    {
        $this->client = new \GearmanClient();
    }

    /**
     * Add gearman server to connection pool
     * @param string $host
     * @param int $port
     */
    public function addServer($host = '127.0.0.1', $port = 4730)
    {
        $this->client->addServer($host, $port);
    }

    /**
     * Put task in queue and synchronously wait for result
     *
     * @param string $queue
     * @param mixed $workload
     * @return mixed worker call result
     * @throws ProcessingException
     */
    public function rpc($queue, $workload)
    {
        $result = $this->client->doNormal($queue, serialize($workload));
        $returnCode = $this->client->returnCode();
        if ($returnCode !== GEARMAN_SUCCESS || !is_string($result) || $result == '') {
            throw new ProcessingException("Queue processing returned with result: {$result}");
        }
        $result = unserialize($result);
        return $result;
    }
}
