<?php

namespace Makaira\Connect;

use Makaira\Connect\Type\Common\ChangeDatum;

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
