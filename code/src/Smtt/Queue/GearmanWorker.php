<?php

namespace Smtt\Queue;

class GearmanWorker implements WorkerInterface
{
    /** @var \GearmanWorker */
    protected $worker;

    /**
     * GearmanWorker constructor.
     */
    public function __construct()
    {
        $this->worker = new \GearmanWorker();
    }

    public function addServer($host = '127.0.0.1', $port = 4730)
    {
        $this->worker->addServer($host, $port);
    }

    /**
     * @param string $queue
     * @param callable $handler
     */
    public function addHandler($queue, callable $handler)
    {
        $realHandler = function (\GearmanJob $job) use ($handler) {
            $workload = unserialize($job->workload());
            try {
                $result = call_user_func($handler, $workload);
                $job->sendComplete(serialize($result));
                return serialize($result);
            } catch (\Exception $e) {
                $job->sendFail();
                return false;
            }
        };
        $this->worker->addFunction($queue, $realHandler);
    }

    public function handle()
    {
        return $this->worker->work();
    }
}
