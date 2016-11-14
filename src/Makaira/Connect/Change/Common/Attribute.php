<?php

namespace Makaira\Connect\Change\Common;

/**
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class Attribute extends \Kore\DataObject\DataObject
{
    public $active = 0;
    public $oxid;
    public $oxtitle;
    public $oxpos;
    public $oxvalue;
}
