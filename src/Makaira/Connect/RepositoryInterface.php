<?php
namespace Makaira\Connect;

use Makaira\Connect\Result\Changes;
use Makaira\Connect\Type;

interface RepositoryInterface
{
    /**
     * Fetch and serialize changes.
     * @param int $since Sequence offset
     * @param int $limit Fetch limit
     * @return Changes
     */
    public function getChangesSince($since, $limit = 50);

    /**
     * Mark an object as updated.
     * @param string $oxid
     */
    public function touch($oxid);

    /**
     * Mark an object as deleted.
     * @param string $oxid
     */
    public function delete($oxid);

    /**
     * Check if an object has been marked as deleted.
     * @param string $oxid
     * @return bool
     */
    public function isDeleted($oxid);

    /**
     * Add a modifier.
     * @param Modifier $modifier
     */
    public function addModifier(Modifier $modifier);

    /**
     * Apply modifiers to datum.
     * @param Type $datum
     * @param DatabaseInterface $database
     * @return Type
     */
    public function applyModifiers(Type $datum, DatabaseInterface $database);
}
