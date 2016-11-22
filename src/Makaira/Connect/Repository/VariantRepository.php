<?php

namespace Makaira\Connect\Repository;

use Makaira\Connect\Change;
use Makaira\Connect\DatabaseInterface;
use Makaira\Connect\RepositoryInterface;
use Makaira\Connect\Type\Variant\Variant;

class VariantRepository implements RepositoryInterface
{
    protected $selectQuery = "
        SELECT
            oxarticles.oxparentid AS `parent`,
            UNIX_TIMESTAMP(oxarticles.oxtimestamp) AS `timestamp`,
            oxarticles.*,
            oxartextends.oxlongdesc AS `OXLONGDESC`,
            oxartextends.oxtags AS `OXTAGS`
        FROM
            oxarticles
            LEFT JOIN oxartextends ON oxarticles.oxid = oxartextends.oxid
        WHERE
            oxarticles.oxid = :id
            AND oxarticles.oxparentid != ''
    ";
    protected $touchQuery = "
        INSERT INTO
          makaira_connect_changes
        (OXID, TYPE, CHANGED)
          VALUES
        (:oxid, 'variant', NOW())
    ";
    protected $deleteQuery = "
        REPLACE INTO
          makaira_connect_deletions
        (OXID, TYPE, CHANGED)
          VALUES
        (:oxid, 'variant', NOW())
    ";
    protected $undeleteQuery = "
        DELETE FROM
          makaira_connect_deletions
        WHERE
          OXID = :oxid
          AND TYPE = 'variant'
    ";
    protected $isDeletedQuery = "
        SELECT * FROM
          makaira_connect_deletions
        WHERE
          OXID = :oxid
          AND TYPE = 'variant'
        LIMIT 1
    ";
    /**
     * @var DatabaseInterface
     */
    private $database;
    /**
     * @var ModifierList
     */
    private $modifiers;

    public function __construct(DatabaseInterface $database, ModifierList $modifiers)
    {
        $this->database = $database;
        $this->modifiers = $modifiers;
    }

    public function get($id)
    {
        $result = $this->database->query($this->selectQuery, ['id' => $id]);

        $change = new Change();

        if (empty($result)) {
            $change->deleted = true;
            return $change;
        }
        $variant = new Variant($result[0]);
        $variant = $this->modifiers->applyModifiers($variant);
        $change->data = $variant;
        return $change;
    }

    /**
     * Mark an object as updated.
     *
     * @param string $oxid
     *
     * @codeCoverageIgnore
     */
    public function touch($oxid)
    {
        $this->database->execute($this->touchQuery, ['oxid' => $oxid]);
        $this->database->execute($this->undeleteQuery, ['oxid' => $oxid]);
    }

    /**
     * Mark an object as deleted.
     *
     * @param string $oxid
     *
     * @codeCoverageIgnore
     */
    public function delete($oxid)
    {
        $this->database->execute($this->touchQuery, ['oxid' => $oxid]);
        $this->database->execute($this->deleteQuery, ['oxid' => $oxid]);
    }

    /**
     * Check if an object has been marked as deleted.
     *
     * @param string $oxid
     *
     * @return bool
     */
    public function isDeleted($oxid)
    {
        $result = $this->database->query($this->isDeletedQuery, ['oxid' => $oxid]);

        return count($result) > 0;
    }
}
