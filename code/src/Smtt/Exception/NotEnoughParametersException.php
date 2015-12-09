<?php

namespace Smtt\Exception;

class NotEnoughParametersException extends \RuntimeException implements Exception
{
    public function __construct($message, \Exception $previous = null)
    {
        parent::__construct($message, 400, $previous);
    }
}
