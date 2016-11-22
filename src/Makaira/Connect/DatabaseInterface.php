<?php

namespace Makaira\Connect;

/**
 * Simple database facade so we do not have to access either PDO nor oxDb directly.
 * @version $Revision$
 */
interface DatabaseInterface
{
    /**
     * Query database in ADODB_FETCH_ASSOC mode
     *
     * Will replace ':parameter' in the query with the quoted value from the
     * $parameters array.
     *
     * @param string $query
     * @param array $parameters
     * @return array
     */
    public function query($query, array $parameters = array());

    /**
     * Execute query without return value
     *
     * Will replace ':parameter' in the query with the quoted value from the
     * $parameters array.
     *
     * @param string $query
     * @param array $parameters
     * @return void
     */
    public function execute($query, array $parameters = array());
}
