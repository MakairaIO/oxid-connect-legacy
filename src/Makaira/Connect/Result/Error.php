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
    public $endpoint = 'Oxid Makaira Connect';
    public $message = null;
    public $file = null;
    public $line = null;
    public $stack = [];

    public function __construct($message)
    {
        parent::__construct(['message' => $message]);
    }
}
