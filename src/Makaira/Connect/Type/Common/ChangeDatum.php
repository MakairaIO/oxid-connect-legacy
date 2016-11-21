<?php

namespace Makaira\Connect\Type\Common;


use Kore\DataObject\DataObject;

class ChangeDatum extends DataObject
{
    public $id;
    public $timestamp;
    public $shop = [];
    public $active = true;
    public $OXID;
}
