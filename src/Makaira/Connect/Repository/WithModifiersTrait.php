<?php

namespace Makaira\Connect\Repository;

use Makaira\Connect\DatabaseInterface;
use Makaira\Connect\Type;
use Makaira\Connect\Modifier;

trait WithModifiersTrait
{
    /** @var Modifier[] */
    private $modifiers = [];

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
     * @param Type $datum
     * @param DatabaseInterface $database
     * @return Type
     */
    public function applyModifiers(Type $datum, DatabaseInterface $database)
    {
        foreach ($this->modifiers as $modifier) {
            $datum = $modifier->apply($datum, $database);
        }
        return $datum;
    }

}
