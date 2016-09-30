<?php

namespace Makaira\Connect\Result;

use Makaira\Connect\Result;

class Changes extends Result
{
    public $since = null;
    public $count = 0;
    public $changes = array();
}
