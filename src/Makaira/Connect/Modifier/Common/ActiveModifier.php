<?php

namespace Makaira\Connect\Modifier\Common;

use Makaira\Connect\Modifier;
use Makaira\Connect\Type;
use Makaira\Connect\Type\Product\Product;

class ActiveModifier extends Modifier
{
    /**
     * Modify product and return modified product
     *
     * @param Product $product
     *
     * @return BaseProduct
     */
    public function apply(Type $product)
    {
        $product->active = (bool) $product->OXACTIVE;
        if (isset($product->OXHIDDEN)) {
            $product->active = $product->active && !(bool) $product->OXHIDDEN;
        }

        return $product;
    }
}
