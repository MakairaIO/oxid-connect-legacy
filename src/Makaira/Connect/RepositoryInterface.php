<?php
namespace Makaira\Connect;

use Makaira\Connect\Result\Changes;

interface RepositoryInterface
{
    /**
     * Fetch and serialize changes.
     *
     * @param int $since Sequence offset
     * @param int $limit Fetch limit
     *
     * @return Changes
     */
    public function getChangesSince($since, $limit = 50);

    /**
     * Mark an object as updated.
     *
     * @param string $oxid
     */
    public function touch($oxid);

    /**
     * Mark an object as deleted.
     *
     * @param string $oxid
     */
    public function delete($oxid);

    /**
     * Check if an object has been marked as deleted.
     *
     * @param string $oxid
     *
     * @return bool
     */
    public function isDeleted($oxid);
}
