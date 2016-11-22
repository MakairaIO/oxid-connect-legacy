<?php

namespace Makaira\Connect;

/**
 * Class Change
 *
 * @package Makaira\Connect
 * @codeCoverageIgnore
 */
class Change extends \Kore\DataObject\DataObject
{
    public $id;
    public $sequence;
    public $deleted = false;
    public $data;
}
