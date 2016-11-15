<?php

namespace Makaira\Connect\Change\Product;


use Makaira\Connect\DatabaseInterface;

class LongDescriptionModifier extends Modifier
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
        $product->OXLONGDESC = trim(strip_tags($product->OXLONGDESC));
        return $product;
    }
}
