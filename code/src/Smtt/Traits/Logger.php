<?php
namespace Smtt\Traits;

use DI\Annotation\Inject;
use Psr\Log\LoggerInterface;

trait Logger
{
    /** @var LoggerInterface */
    protected $logger;

    /**
     * @Inject
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
}
