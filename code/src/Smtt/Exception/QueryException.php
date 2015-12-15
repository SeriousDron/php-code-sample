<?php

namespace Smtt\Exception;

class QueryException extends \RuntimeException implements Exception
{
    public function __construct($message, \Exception $previous = null)
    {
        parent::__construct($message, 500, $previous);
    }
}
