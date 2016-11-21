<?php

namespace Makaira\Connect\Type\Common;

use Makaira\Connect\DatabaseInterface;

abstract class Modifier
{
    /**
     * Modify product and return modified product
     *
     * @param ChangeDatum $datum
     * @param DatabaseInterface $database
     * @return ChangeDatum
     */
    abstract public function apply(ChangeDatum $datum, DatabaseInterface $database);
}
