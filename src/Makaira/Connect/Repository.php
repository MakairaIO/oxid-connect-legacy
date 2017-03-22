<?php

namespace Makaira\Connect;

use Makaira\Connect\Result\Changes;

class Repository
{
    protected $cleanupQuery = "
        DELETE FROM
          makaira_connect_changes
        WHERE
          SEQUENCE NOT IN (
            SELECT S FROM (
              SELECT
                MAX(SEQUENCE) AS S
              FROM 
                makaira_connect_changes
              GROUP BY
                OXID, TYPE
            ) AS c
          )
    ";
    /**
     * @var DatabaseInterface
     */
    private $database;

    protected $selectQuery = "
        SELECT
            makaira_connect_changes.sequence,
            makaira_connect_changes.oxid AS `id`,
            makaira_connect_changes.type
        FROM
            makaira_connect_changes
        WHERE
            makaira_connect_changes.sequence > :since
        ORDER BY
            sequence ASC
        LIMIT :limit
    ";

    protected $touchQuery = "
        INSERT INTO
          makaira_connect_changes
        (OXID, TYPE, CHANGED)
          VALUES
        (:id, :type, NOW());
    ";

    public function __construct(DatabaseInterface $database, array $repositoryMapping = array())
    {
        $this->database = $database;
        $this->repositoryMapping = $repositoryMapping;
    }

    /**
     * Fetch and serialize changes.
     * @param int $since Sequence offset
     * @param int $limit Fetch limit
     * @return Changes
     */
    public function getChangesSince($since, $limit = 50)
    {
        $result = $this->database->query($this->selectQuery, ['since' => $since ?: 0, 'limit' => $limit]);

        $changes = array();
        foreach ($result as $row) {
            $change = $this->getRepositoryForType($row['type'])->get($row['id']);
            $change->id = $row['id'];
            $change->sequence = $row['sequence'];
            $change->type = $row['type'];
            $changes[] = $change;
        }

        return new Changes(array(
            'since' => $since,
            'count' => count($changes),
            'changes' => $changes,
        ));
    }

    public function countChangesSince($since)
    {
        $result = $this->database->query(
            'SELECT
                COUNT(*) count
            FROM
                makaira_connect_changes
            WHERE
                makaira_connect_changes.sequence > :since',
            ['since' => $since ?: 0]
        );

        return $result[0]['count'];
    }

    protected function getRepositoryForType($type)
    {
        if (!isset($this->repositoryMapping[$type])) {
            throw new \OutOfBoundsException("No repository defined for type " . $type);
        }

        return $this->repositoryMapping[$type];
    }

    /**
     * Mark an object as updated.
     *
     * @param string $type
     * @param string $id
     */
    public function touch($type, $id)
    {
        $this->database->execute($this->touchQuery, ['type' => $type, 'id' => $id]);
    }

    /**
     * Clean up changes list.
     * @ignoreCodeCoverage
     */
    public function cleanup()
    {
        $this->database->execute($this->cleanupQuery);
    }

    /**
     * Add all items to the changes list.
     */
    public function touchAll()
    {
        /**
         * @var string $type
         * @var RepositoryInterface $repository
         */
        foreach ($this->repositoryMapping as $type => $repository) {
            foreach ($repository->getAllIds() as $id) {
                $this->touch($type, $id);
            }
        }
    }
}
