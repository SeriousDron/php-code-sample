<?php

namespace Smtt\Service;

use Smtt\dto\MoRequest;
use Smtt\dto\RegisterResult;

interface RegisterMoInterface
{
    /**
     * @param MoRequest $moRequest
     * @return RegisterResult
     */
    public function register(MoRequest $moRequest);
}
