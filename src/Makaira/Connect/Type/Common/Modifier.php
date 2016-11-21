<?php

namespace Makaira\Connect\Type\Common;

use Makaira\Connect\DatabaseInterface;
use Makaira\Connect\Type\ChangeDatum;

abstract class Modifier
{
    /**
     * Modify product and return modified product
     *
     * @param ChangeDatum $product
     * @param DatabaseInterface $database
     * @return BaseProduct
     */
    abstract public function apply(ChangeDatum $product, DatabaseInterface $database);
}
