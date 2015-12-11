<?php

namespace Smtt\RegisterMo;

use Smtt\dto\MoRequest;

interface RegisterMoInterface
{
    /**
     * @param MoRequest $moRequest
     * @return boolean
     */
    public function register(MoRequest $moRequest);
}
