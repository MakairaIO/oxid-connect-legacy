<?php

namespace Makaira\Connect\Change\Product;

use Makaira\Connect\DatabaseInterface;

abstract class Modifier
{
    /**
     * Modify product and return modified product
     *
     * @param LegacyProduct $product
     * @param DatabaseInterface $database
     * @return LegacyProduct
     */
    abstract public function apply(LegacyProduct $product, DatabaseInterface $database);
}
