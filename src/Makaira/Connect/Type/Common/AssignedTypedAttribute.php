<?php

namespace Makaira\Connect\Type\Common;

use Kore\DataObject\DataObject;

/**
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class AssignedTypedAttribute extends DataObject
{
    public $active = 0;
    public $id;
    public $title;
    public $value;
}
