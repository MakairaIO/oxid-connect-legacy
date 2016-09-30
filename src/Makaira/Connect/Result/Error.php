<?php

namespace Makaira\Connect\Result;

use Makaira\Connect\Result;

class Error extends Result
{
    public $ok = false;
    public $message = null;

    public function __construct($message)
    {
        $this->message = $message;
    }
}
