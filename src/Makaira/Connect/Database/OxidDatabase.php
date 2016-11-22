<?php

namespace Makaira\Connect\Database;

use Makaira\Connect\DatabaseInterface;

/**
 * Simple database facade so we do not need to set fetch mode before each query
 *
 * @version $Revision$
 */
class OxidDatabase implements DatabaseInterface
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
        $this->database->setFetchMode(ADODB_FETCH_ASSOC);
        return $this->database->getAll($this->replaceQueryParameters($query, $parameters));
    }

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
    public function execute($query, array $parameters = array())
    {
        $this->database->execute($this->replaceQueryParameters($query, $parameters));
    }

    protected function replaceQueryParameters($query, array $parameters)
    {
        foreach ($parameters as $key => $value) {
            if (!is_numeric($value)) {
                $parameters[$key] = $this->database->quote($value);
            }
        }

        return preg_replace_callback(
            '(:(?P<key>[A-Za-z0-9]+)(?P<end>[^A-Za-z0-9]|$))',
            function ($match) use ($parameters) {
                if (!isset($parameters[$match['key']])) {
                    throw new \OutOfBoundsException("Parameter for " . $match['key'] . " missing.");
                }

                return $parameters[$match['key']] . $match['end'];
            },
            $query
        );
    }
}
