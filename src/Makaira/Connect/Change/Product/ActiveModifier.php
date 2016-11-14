<?php

namespace Makaira\Connect\Change\Product;


use Makaira\Connect\Database;

class ActiveModifier extends Modifier
{

    /**
     * Modify product and return modified product
     *
     * @param LegacyProduct $product
     * @param Database $database
     * @return LegacyProduct
     */
    public function apply(LegacyProduct $product, Database $database)
    {
        $product->active = (bool)$product->OXACTIVE;
        if (isset($product->OXHIDDEN)) {
            $product->active = $product->active && !(bool)$product->OXHIDDEN;
        }
        return $product;
    }
}
