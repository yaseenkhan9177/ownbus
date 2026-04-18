<?php

namespace App\Exceptions;

use Exception;

/**
 * DataLockedException
 * Thrown when an attempt is made to modify a record that is finalized or in a closed period.
 */
class DataLockedException extends Exception
{
    public function __construct($message = "This record is locked and cannot be modified.")
    {
        parent::__construct($message);
    }
}
