<?php

namespace Smtt\Exception;

/**
 * {@inheritDoc}
 */
class UnexpectedValueException extends \UnexpectedValueException implements Exception
{
    public function __construct($message, \Exception $previous = null)
    {
        parent::__construct($message, 400, $previous);
    }
}
