<?php

namespace Makaira\Connect\Repository;


use Makaira\Connect\DatabaseInterface;
use Makaira\Connect\Type\Common\ChangeDatum;
use Makaira\Connect\Type\Common\Modifier;

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
     * @param ChangeDatum $datum
     * @param DatabaseInterface $database
     * @return ChangeDatum
     */
    public function applyModifiers(ChangeDatum $datum, DatabaseInterface $database)
    {
        foreach ($this->modifiers as $modifier) {
            $datum = $modifier->apply($datum, $database);
        }
        return $datum;
    }

}
