<?php

namespace Makaira\Connect\Database;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Statement;
use Makaira\Connect\DatabaseInterface;
use Makaira\Connect\Utils\TableTranslator;

/**
 * Simple database facade so we do not need to set fetch mode before each query
 *
 * @version $Revision$
 */
class DoctrineDatabase implements DatabaseInterface
{
    /**
     * @var Connection
     */
    private $database;

    /**
     * @var Statement[]
     */
    private $preparedStatements = [];

    /** @var  TableTranslator */
    private $translator;

    public function __construct(Connection $database, TableTranslator $translator)
    {
        $this->database   = $database;
        $this->translator = $translator;
    }

    /**
     * Execute query without return value
     * Will replace ':parameter' in the query with the quoted value from the
     * $parameters array.
     *
     * @param string $query
     * @param array  $parameters
     *
     * @return void
     */
    public function execute($query, array $parameters = array(), $translateTables = true)
    {
        if ($translateTables) {
            $query = $this->translator->translate($query);
        }
        $cacheKey = md5($query);
        if (!isset($this->preparedStatements[$cacheKey])) {
            $this->preparedStatements[$cacheKey] = $this->database->prepare($query);
        }
        $statement = $this->preparedStatements[$cacheKey];
        $statement = $this->bindQueryParameters($statement, $parameters);

        $statement->execute();
    }

    /**
     * Query database in ADODB_FETCH_ASSOC mode
     * Will replace ':parameter' in the query with the quoted value from the
     * $parameters array.
     *
     * @param string $query
     * @param array  $parameters
     *
     * @return array
     */
    public function query($query, array $parameters = array(), $translateTables = true)
    {
        if ($translateTables) {
            $query = $this->translator->translate($query);
        }
        $cacheKey = md5($query);
        if (!isset($this->preparedStatements[$cacheKey])) {
            $this->preparedStatements[$cacheKey] = $this->database->prepare($query);
        }
        $statement = $this->preparedStatements[$cacheKey];
        $statement = $this->bindQueryParameters($statement, $parameters);

        $statement->execute();

        $result = $statement->fetchAll();
        foreach ($result as $nr => $row) {
            $column = 0;
            foreach ($row as $key => $field) {
                $meta = $statement->getWrappedStatement()->getColumnMeta($column++);

                switch ($meta['native_type']) {
                    case 'TINY':
                    case 'LONG':
                    case 'LONGLONG':
                        $result[$nr][$key] = (int) $field;
                        break;
                    case 'DOUBLE':
                        $result[$nr][$key] = (float) $field;
                        break;
                }
            }
        }

        return $result;
    }

    public function getColumn($query, array $parameters = [])
    {
        $statement = $this->database->prepare($query);
        $statement = $this->bindQueryParameters($statement, $parameters);

        $statement->execute();

        return $statement->fetchAll(\PDO::FETCH_COLUMN);
    }

    protected function bindQueryParameters(Statement $statement, array $parameters)
    {
        foreach ($parameters as $key => $value) {
            switch (gettype($value)) {
                case 'integer':
                    $statement->bindValue($key, $value, \PDO::PARAM_INT);
                    break;
                case 'boolean':
                    $statement->bindValue($key, $value, \PDO::PARAM_BOOL);
                    break;
                case 'NULL':
                    $statement->bindValue($key, $value, \PDO::PARAM_NULL);
                    break;
                default:
                    $statement->bindValue($key, $value);
                    break;
            }
        }

        return $statement;
    }
}
