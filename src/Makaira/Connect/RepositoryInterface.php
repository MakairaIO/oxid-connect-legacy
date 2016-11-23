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
    public function get($id);

    /**
     * Get TYPE of repository.
     * @return string
     */
    public function getType();

    /**
     * Get all IDs handled by this repository.
     * @return string[]
     */
    public function getAllIds();
}
