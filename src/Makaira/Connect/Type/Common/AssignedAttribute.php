<?php

namespace Makaira\Connect\Type\Common;
use Makaira\Connect\Type\ChangeDatum;

/**
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class AssignedAttribute extends ChangeDatum
{
    public $active = 0;
    public $oxid;
    public $oxtitle;
    public $oxpos;
    public $oxvalue;
}
