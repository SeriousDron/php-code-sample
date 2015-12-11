<?php

namespace Smtt\Queue;

interface WorkerInterface
{
    /**
     * Add callback function to process task from queue
     * @param string $queue
     * @param callable $handler
     */
    public function addHandler($queue, callable $handler);

    /**
     * Process task from queue
     * @return bool Is handling successfule
     */
    public function handle();
}
