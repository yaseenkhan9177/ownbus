<?php

namespace App\Exceptions;

use Exception;

class InvalidStateTransitionException extends Exception
{
    public function __construct($from, $to)
    {
        parent::__construct("Invalid status transition from '{$from}' to '{$to}'.");
    }
}
