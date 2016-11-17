<?php

namespace Makaira\Connect\Result;

use Makaira\Connect\Result;

/**
 * Class Changes
 * @package Makaira\Connect\Result
 * @codeCoverageIgnore
 */
class Changes extends Result
{
    public $type = null;
    public $since = null;
    public $count = 0;
    public $changes = array();
}
