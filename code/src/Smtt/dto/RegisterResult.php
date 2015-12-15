<?php

namespace Smtt\dto;

class RegisterResult
{
    /** @var bool */
    public $successful = false;

    /** @var string */
    public $message = '';

    /**
     * RegisterResult constructor.
     * @param bool $successful
     */
    public function __construct($successful)
    {
        $this->successful = $successful;
    }


    public static function success()
    {
        $result = new self(true);
        $result->successful = true;
        return $result;
    }

    public static function fail($message)
    {
        $result = new self(false);
        $result->successful = false;
        $result->message = $message;
        return $result;
    }
}
