<?php
/**
 * This file is part of a marmalade GmbH project
 * It is not Open Source and may not be redistributed.
 * For contact information please visit http://www.marmalade.de
 * Version:    1.0
 * Author:     Jens Richter <richter@marmalade.de>
 * Author URI: http://www.marmalade.de
 */

namespace Makaira\Connect\Modifier\Common;

use Makaira\Connect\Modifier;
use Makaira\Connect\Type;

class BlacklistModifier extends Modifier
{
    private $blacklistedFields =  [];

    public function __construct(array $blacklistedFields = [])
    {
        $this->blacklistedFields = $blacklistedFields;
    }

    /**
     * Modify product and return modified product
     *
     * @param Type $type
     *
     * @return Type
     */
    public function apply(Type $type)
    {
        foreach ($this->blacklistedFields as $blacklistedField) {
            if (isset($type->$blacklistedField)) {
                unset($type->$blacklistedField);
            } elseif (array_key_exists($blacklistedField, $type->additionalData)) {
                unset($type->additionalData[$blacklistedField]);
            }
        }

        return $type;
    }
}
