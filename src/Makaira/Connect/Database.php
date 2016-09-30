<?php

namespace Makaira\Connect;

/**
 * Simple database facade so we do not need to set fetch mode before each query
 *
 * @version $Revision$
 */
class Database
{
    /**
     * @var \oxLegacyDb
     */
    private $database;

    public function __construct(\oxLegacyDb $database)
    {
        $this->database = $database;
    }

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
    public function query($query, array $parameters = array())
    {
        foreach ($parameters as $key => $value) {
            if (!is_numeric($value)) {
                $value = $this->database->quote($value);
            }
            $query = str_replace(':' . $key, $value, $query);
        }

        $this->database->setFetchMode(ADODB_FETCH_ASSOC);
        return $this->database->getAll($query);
    }
}
