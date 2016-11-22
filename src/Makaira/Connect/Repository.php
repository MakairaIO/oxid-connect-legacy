<?php

namespace Makaira\Connect;

use Makaira\Connect\Result\Changes;

class Repository
{
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

    protected $deleteQuery = "
        REPLACE INTO
          makaira_connect_deletions
        (OXID, TYPE, CHANGED)
          VALUES
        (:id, :type, NOW())
    ";

    protected $undeleteQuery = "
        DELETE FROM
          makaira_connect_deletions
        WHERE
          OXID = :id
          AND TYPE = :type
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
    public function getChangesSince($since, $limit = 50) {
        $result = $this->database->query($this->selectQuery, ['since' => $since, 'limit' => $limit]);

        $changes = array();
        foreach ($result as $row) {
            $change = $this->getRepositoryForType($row['type'])->get($row['id']);
            $change->id = $row['id'];
            $change->sequence = $row['sequence'];
            $change->type = $row['type'];
            $changes[] = $change;
        }

        return new Changes( array(
            'since' => $since,
            'count' => count($changes),
            'changes' => $changes,
        ));
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
        $this->database->execute($this->undeleteQuery, ['type' => $type, 'id' => $id]);
    }

    /**
     * Mark an object as deleted.
     *
     * @param string $type
     * @param string $id
     */
    public function delete($type, $id)
    {
        $this->database->execute($this->touchQuery, ['type' => $type, 'id' => $id]);
        $this->database->execute($this->deleteQuery, ['type' => $type, 'id' => $id]);
    }
}
