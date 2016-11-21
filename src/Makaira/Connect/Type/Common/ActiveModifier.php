<?php

namespace Makaira\Connect\Type\Common;


use Makaira\Connect\DatabaseInterface;
use Makaira\Connect\Type\ChangeDatum;
use Makaira\Connect\Type\Product\Product;

class ActiveModifier extends Modifier
{

    /**
     * Modify product and return modified product
     *
     * @param Product $product
     * @param DatabaseInterface $database
     * @return BaseProduct
     */
    public function apply(ChangeDatum $product, DatabaseInterface $database)
    {
        $product->active = (bool)$product->OXACTIVE;
        if (isset($product->OXHIDDEN)) {
            $product->active = $product->active && !(bool)$product->OXHIDDEN;
        }
        return $product;
    }
}
