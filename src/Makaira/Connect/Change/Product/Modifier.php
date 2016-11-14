<?php

namespace Makaira\Connect\Change\Product;

use Makaira\Connect\Database;

abstract class Modifier
{
    /**
     * Modify product and return modified product
     *
     * @param LegacyProduct $product
     * @param Database $database
     * @return LegacyProduct
     */
    abstract public function apply(LegacyProduct $product, Database $database);
}
