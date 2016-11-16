<?php

namespace Makaira\Connect\Repository;


use Makaira\Connect\DatabaseInterface;
use Makaira\Connect\Result\Changes;
use Makaira\Connect\Type\Common\Modifier;

class VariantRepository implements RepositoryInterface
{
    /**
     * @var DatabaseInterface
     */
    private $database;

    /**
     * @var Modifier[]
     */
    private $modifiers = [];

    protected $touchQuery = "
        INSERT INTO
          makaira_connect_changes
        (OXID, TYPE, CHANGED)
          VALUES
        (:oxid, 'variant', NOW());
        ";

    /**
     * VariantRepository constructor.
     * @param DatabaseInterface $database
     * @param \Makaira\Connect\Type\Common\Modifier[] $modifiers
     */
    public function __construct(DatabaseInterface $database, array $modifiers)
    {
        $this->database = $database;
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
     * Fetch and serialize changes.
     * @param int $since Sequence offset
     * @param int $limit Fetch limit
     * @return Changes
     */
    public function getChangesSince($since, $limit = 50)
    {
        // TODO: Implement getChangesSince() method.
    }

    /**
     * Mark an object as updated.
     * @param string $oxid
     */
    public function touch($oxid)
    {
        $this->database->query($this->touchQuery, ['oxid' => $oxid]);
    }
}
