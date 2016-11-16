<?php

namespace Makaira\Connect\Type\Common;

use Makaira\Connect\DatabaseInterface;

abstract class Modifier
{
    /**
     * Modify product and return modified product
     *
     * @param BaseProduct $product
     * @param DatabaseInterface $database
     * @return BaseProduct
     */
    abstract public function apply(BaseProduct $product, DatabaseInterface $database);
}
