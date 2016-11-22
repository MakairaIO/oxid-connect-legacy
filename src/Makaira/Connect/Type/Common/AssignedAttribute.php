<?php

namespace Makaira\Connect\Type\Common;

use Kore\DataObject\DataObject;

/**
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class AssignedAttribute extends DataObject
{
    public $active = 0;
    public $oxid;
    public $oxtitle;
    public $oxpos;
    public $oxvalue;
}
