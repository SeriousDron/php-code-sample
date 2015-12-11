<?php

namespace Smtt\Queue;

class GearmanWorker implements WorkerInterface
{
    /** @var \GearmanWorker */
    protected $worker;

    /**
     * @param string $queue
     * @param callable $handler
     */
    public function addHandler($queue, callable $handler)
    {
        $realHandler = function (\GearmanJob $job) use ($handler) {
            $workload = unserialize($job->workload());
            return call_user_func($handler, $workload);
        };
        $this->worker->addFunction($queue, $realHandler);
    }

    public function handle()
    {
        $this->worker->work();
    }
}
