<?php

namespace Makaira\Connect\Types\Common;


use Makaira\Connect\DatabaseInterface;

class LongDescriptionModifier extends Modifier
{

    /**
     * Modify product and return modified product
     *
     * @param BaseProduct $product
     * @param DatabaseInterface $database
     * @return BaseProduct
     */
    public function apply(BaseProduct $product, DatabaseInterface $database)
    {
        $product->OXLONGDESC = trim(strip_tags($product->OXLONGDESC));
        return $product;
    }
}
