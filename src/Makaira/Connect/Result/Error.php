<?php

namespace Makaira\Connect\Result;

use Makaira\Connect\Result;

/**
 * Class Error
 *
 * @package Makaira\Connect\Result
 * @codeCoverageIgnore
 */
class Error extends Result
{
    public $ok = false;
    public $message = null;

    public function __construct($message)
    {
        parent::__construct(['message' => $message]);
    }
}
