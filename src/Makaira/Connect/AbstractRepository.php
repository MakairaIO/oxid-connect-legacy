<?php

namespace Makaira\Connect\Repository;

use Makaira\Connect\Change;
use Makaira\Connect\DatabaseInterface;

abstract class AbstractRepository
{
    /**
     * @var DatabaseInterface
     */
    protected $database;

    /**
     * @var ModifierList
     */
    protected $modifiers;

    public function __construct(DatabaseInterface $database, ModifierList $modifiers)
    {
        $this->database  = $database;
        $this->modifiers = $modifiers;
    }

    public function get($id)
    {
        $result = $this->database->query($this->getSelectQuery(), ['id' => $id]);

        $change = new Change();

        if (empty($result)) {
            $change->deleted = true;

            return $change;
        }

        $type         = $this->getInstance($result[0]);
        $type         = $this->modifiers->applyModifiers($type, $this->database);
        $change->data = $type;

        return $change;
    }

    /**
     * Get all IDs handled by this repository.
     *
     * @return string[]
     */
    public function getAllIds()
    {
        $result = $this->database->query($this->getAllIdsQuery());

        return array_map(
            function ($row) {
                return $row['OXID'];
            },
            $result
        );
    }

    abstract protected function getType();

    abstract protected function getInstance($id);

    abstract protected function getSelectQuery();

    abstract protected function getAllIdsQuery();
}
