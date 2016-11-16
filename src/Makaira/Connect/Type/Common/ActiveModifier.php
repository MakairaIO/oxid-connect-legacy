<?php

namespace Makaira\Connect\Type\Common;


use Makaira\Connect\DatabaseInterface;

class ActiveModifier extends Modifier
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
        $product->active = (bool)$product->OXACTIVE;
        if (isset($product->OXHIDDEN)) {
            $product->active = $product->active && !(bool)$product->OXHIDDEN;
        }
        return $product;
    }
}
