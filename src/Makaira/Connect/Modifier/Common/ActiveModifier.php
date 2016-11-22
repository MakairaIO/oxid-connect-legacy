<?php

namespace Makaira\Connect\Modifier\Common;


use Makaira\Connect\DatabaseInterface;
use Makaira\Connect\Type\Product\Product;
use Makaira\Connect\Type\Common\ChangeDatum;
use Makaira\Connect\Modifier;

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
