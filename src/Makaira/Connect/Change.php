<?php

namespace Makaira\Connect;

class Change extends \Kore\DataObject\DataObject
{
    public $id;
    public $sequence;
    public $deleted = false;
    public $data;
}
