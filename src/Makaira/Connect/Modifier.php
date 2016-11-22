<?php

namespace Makaira\Connect;

abstract class Modifier
{
    /**
     * Modify product and return modified product
     *
     * @param Type $type
     * @param DatabaseInterface $database
     * @return Type
     */
    abstract public function apply(Type $type, DatabaseInterface $database);
}
