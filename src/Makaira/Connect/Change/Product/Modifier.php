<?php

namespace Makaira\Connect\Change\Product;

use Makaira\Connect\Change\LegacyProduct;

abstract class Modifier
{
    /**
     * Modify product and return modified product
     *
     * @param LegacyProduct $product
     * @return LegacyProduct
     */
    abstract public function apply(LegacyProduct $product);
}
