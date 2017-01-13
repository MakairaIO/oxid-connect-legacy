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

    public function __set($name, $value)
    {
        try {
            parent::__set($name, $value);
        } catch (\Exception $e) {
            // catch exception on unknown fields, just drop them
            // TODO: add field whitelisting for allowed additional fields
        }
    }
}
