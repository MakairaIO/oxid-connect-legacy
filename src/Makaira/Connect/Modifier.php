<?php

namespace Makaira\Connect;

/**
 * Class Modifier
 *
 * @package Makaira\Connect
 * @SuppressWarnings(PHPMD)
 */
abstract class Modifier
{
    private $docType;

    /**
     * Modify product and return modified product
     *
     * @param Type $type
     *
     * @return Type
     */
    abstract public function apply(Type $type);

    protected function getDocType()
    {
        return $this->docType;
    }

    public function setDocType($docType)
    {
        $this->docType = $docType;
    }
}
