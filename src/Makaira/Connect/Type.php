<?php

namespace Makaira\Connect;

use Kore\DataObject\DataObject;

class Type extends DataObject
{
    public $id;
    public $timestamp;
    public $shop = [];
    public $active = true;
    public $OXID;
}
