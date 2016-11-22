<?php

namespace Makaira\Connect\Repository;

use Makaira\Connect\Change;
use Makaira\Connect\DatabaseInterface;
use Makaira\Connect\Result\Changes;
use Makaira\Connect\Type\Category\Category;
use Makaira\Connect\RepositoryInterface;

class CategoryRepository implements RepositoryInterface
{
    /**
     * @var DatabaseInterface
     */
    private $database;

    /**
     * @var ModifierList
     */
    private $modifiers;

    protected $selectQuery = "
      SELECT
        makaira_connect_changes.sequence,
        makaira_connect_changes.oxid AS `id`,
        UNIX_TIMESTAMP(oxcategories.oxtimestamp) AS `timestamp`,
        oxcategories.*
      FROM
        makaira_connect_changes
        LEFT JOIN oxcategories ON oxcategories.oxid = makaira_connect_changes.oxid
      WHERE
        makaira_connect_changes.sequence > :since
        AND makaira_connect_changes.type = 'category'
      ORDER BY
        sequence ASC
      LIMIT :limit
    ";
    protected $touchQuery = "
        INSERT INTO
          makaira_connect_changes
        (OXID, TYPE, CHANGED)
          VALUES
        (:oxid, 'category', NOW())
    ";
    protected $deleteQuery = "
        REPLACE INTO
          makaira_connect_deletions
        (OXID, TYPE, CHANGED)
          VALUES
        (:oxid, 'category', NOW())
    ";
    protected $undeleteQuery = "
        DELETE FROM
          makaira_connect_deletions
        WHERE
          OXID = :oxid
          AND TYPE = 'category'
    ";
    protected $isDeletedQuery = "
        SELECT * FROM
          makaira_connect_deletions
        WHERE
          OXID = :oxid
          AND TYPE = 'category'
        LIMIT 1
    ";

    public function __construct(DatabaseInterface $database, ModifierList $modifiers)
    {
        $this->database = $database;
        $this->modifiers = $modifiers;
    }

    public function get($id)
    {
        return null;
    }

    /**
     * Fetch and serialize changes.
     * @param int $since Sequence offset
     * @param int $limit Fetch limit
     * @return Changes
     */
    public function getChangesSince($since, $limit = 50)
    {
        $result = $this->database->query($this->selectQuery, ['since' => $since, 'limit' => $limit]);

        $changes = array();
        foreach ($result as $row) {
            $change = new Change();
            $change->id = $row['id'];
            $change->sequence = $row['sequence'];
            unset($row['sequence']);

            if (!isset($row['OXID']) && $this->isDeleted($change->id)) {
                $change->deleted = true;
            } else {
                // @TODO: Do we want to pass the full product / changes list to the modifier to allow aggregated queries?
                $category = new Category($row);
                $category = $this->modifiers->applyModifiers($category, $this->database);
                $change->data = $category;
            }
            $changes[] = $change;
        }

        return new Changes(
            array(
                'type'    => 'category',
                'since'   => $since,
                'count'   => count($changes),
                'changes' => $changes,
            )
        );
    }

    /**
     * Mark an object as updated.
     * @param string $oxid
     * @codeCoverageIgnore
     */
    public function touch($oxid)
    {
        $this->database->execute($this->touchQuery, ['oxid' => $oxid]);
        $this->database->execute($this->undeleteQuery, ['oxid' => $oxid]);
    }

    /**
     * Mark an object as deleted.
     * @param string $oxid
     * @codeCoverageIgnore
     */
    public function delete($oxid)
    {
        $this->database->execute($this->touchQuery, ['oxid' => $oxid]);
        $this->database->execute($this->deleteQuery, ['oxid' => $oxid]);
    }

    /**
     * Check if an object has been marked as deleted.
     * @param string $oxid
     * @return bool
     */
    public function isDeleted($oxid)
    {
        $result = $this->database->query($this->isDeletedQuery, ['oxid' => $oxid]);
        return count($result) > 0;
    }
}
