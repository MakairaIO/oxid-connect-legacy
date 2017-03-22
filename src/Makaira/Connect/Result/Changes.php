<?php

namespace Makaira\Connect\Result;

use Makaira\Connect\Result;

/**
 * Class Changes
 *
 * @package Makaira\Connect\Result
 * @codeCoverageIgnore
 */
class Changes extends Result
{
    public $type = null;
    public $since = null;
    public $count = 0;
    public $changes = array();
    public $language = '';
    //public $active = true; TODO We don't need active here, but we have horrible code doublication anyway...
    public $highLoad = false;
}
