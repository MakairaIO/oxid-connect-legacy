<?php

namespace Makaira\Connect;

/**
 * Simple database facade so we do not have to access either PDO nor oxDb directly.
 *
 * @version $Revision$
 */
interface DatabaseInterface
{
    /**
     * Query database.
     *
     * @param string $query
     * @param array  $parameters
     *
     * @return array
     */
    public function query($query, array $parameters = array());
}
