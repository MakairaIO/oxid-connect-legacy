<?php

namespace Makaira\Connect\Repository;

use Makaira\Connect\Modifier;
use Makaira\Connect\Type;
use Makaira\Connect\Event\ModifierCollectEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ModifierList
{
    /** @var Modifier[] */
    private $modifiers = [];

    public function __construct(string $tag, EventDispatcherInterface $dispatcher)
    {
        $dispatcher->dispatch($tag, new ModifierCollectEvent($this));
    }

    /**
     * Add a modifier.
     *
     * @param Modifier $modifier
     */
    public function addModifier(Modifier $modifier)
    {
        $this->modifiers[] = $modifier;
    }

    /**
     * Apply modifiers to datum.
     *
     * @param Type $type
     *
     * @return Type
     */
    public function applyModifiers(Type $type, $docType)
    {
        foreach ($this->modifiers as $modifier) {
            $modifier->setDocType($docType);
            $type = $modifier->apply($type);
        }

        return $type;
    }
}
