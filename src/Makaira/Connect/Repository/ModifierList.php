<?php

namespace Makaira\Connect\Repository;

use Makaira\Connect\Type;
use Makaira\Connect\Modifier;

class ModifierList
{
    /** @var Modifier[] */
    private $modifiers = [];

    public function __construct(array $modifiers = array())
    {
        foreach ($modifiers as $modifier) {
            $this->addModifier($modifier);
        }
    }

    /**
     * Add a modifier.
     * @param Modifier $modifier
     */
    public function addModifier(Modifier $modifier)
    {
        $this->modifiers[] = $modifier;
    }

    /**
     * Apply modifiers to datum.
     * @param Type $type
     * @return Type
     */
    public function applyModifiers(Type $type)
    {
        foreach ($this->modifiers as $modifier) {
            $type = $modifier->apply($type);
        }
        return $type;
    }
}
