<?php

namespace Makaira\Connect\Change\Product;


use Makaira\Connect\DatabaseInterface;

class ActiveModifier extends Modifier
{

    /**
     * Modify product and return modified product
     *
     * @param LegacyProduct $product
     * @param DatabaseInterface $database
     * @return LegacyProduct
     */
    public function apply(LegacyProduct $product, DatabaseInterface $database)
    {
        $product->active = (bool)$product->OXACTIVE;
        if (isset($product->OXHIDDEN)) {
            $product->active = $product->active && !(bool)$product->OXHIDDEN;
        }
        return $product;
    }
}
