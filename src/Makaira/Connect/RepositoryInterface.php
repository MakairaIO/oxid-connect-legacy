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
    public function get($id);
}
