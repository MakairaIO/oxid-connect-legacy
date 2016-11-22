<?php
namespace Makaira\Connect\Repository;

use Makaira\Connect\DatabaseInterface;
use Makaira\Connect\Result\Changes;
use Makaira\Connect\Type\Common\ChangeDatum;
use Makaira\Connect\Modifier;

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
     * @param ChangeDatum $datum
     * @param DatabaseInterface $database
     * @return ChangeDatum
     */
    public function applyModifiers(ChangeDatum $datum, DatabaseInterface $database);
}
