<?php

namespace Makaira\Connect\Modifier\Common;

use Makaira\Connect\Modifier;
use Makaira\Connect\Type;
use Makaira\Connect\Type\Product\Product;

class ZeroDateTimeModifier extends Modifier
{
    private $zeroDateValues = ['0000-00-00', '0000-00-00 00:00:00'];
    /**
     * Modify product and return modified product
     *
     * @param Type $type
     *
     * @return Type
     */
    public function apply(Type $type)
    {
        foreach ($type as $property => $value) {
            if (is_string($value) && in_array($value, $this->zeroDateValues)) {
                $type->$property = null;
            }
        }

        return $type;
    }
}
