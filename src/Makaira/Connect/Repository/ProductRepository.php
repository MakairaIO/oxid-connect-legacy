<?php

namespace Makaira\Connect\Repository;

use Makaira\Connect\Change;
use Makaira\Connect\DatabaseInterface;
use Makaira\Connect\Result\Changes;
use Makaira\Connect\Type\Common\Modifier;
use Makaira\Connect\Type\Product\Product;
use Makaira\Connect\RepositoryInterface;

class ProductRepository implements RepositoryInterface
{
    use WithModifiersTrait;

    /**
     * @var DatabaseInterface
     */
    private $database;

    protected $selectQuery = "
        SELECT
            makaira_connect_changes.sequence,
            makaira_connect_changes.oxid AS `id`,
            UNIX_TIMESTAMP(oxarticles.oxtimestamp) AS `timestamp`,
            oxarticles.*,
            oxartextends.oxlongdesc as `OXLONGDESC`,
            oxartextends.oxtags as `OXTAGS`,
            oxmanufacturers.oxtitle AS MARM_OXSEARCH_MANUFACTURERTITLE
        FROM
            makaira_connect_changes
            LEFT JOIN oxarticles ON oxarticles.oxid = makaira_connect_changes.oxid
            LEFT JOIN oxartextends ON oxarticles.oxid = oxartextends.oxid
            LEFT JOIN oxmanufacturers ON oxarticles.oxmanufacturerid = oxmanufacturers.oxid
        WHERE
            (oxarticles.oxid is null OR oxarticles.oxparentid = '')
            AND makaira_connect_changes.sequence > :since
            AND makaira_connect_changes.type = 'product'
        ORDER BY
            sequence ASC
        LIMIT :limit
    ";

    protected $touchQuery = "
        INSERT INTO
          makaira_connect_changes
        (OXID, TYPE, CHANGED)
          VALUES
        (:oxid, 'product', NOW());
    ";

    protected $deleteQuery = "
        REPLACE INTO
          makaira_connect_deletions
        (OXID, TYPE, CHANGED)
          VALUES
        (:oxid, 'product', NOW())
    ";

    protected $undeleteQuery = "
        DELETE FROM
          makaira_connect_deletions
        WHERE
          OXID = :oxid
          AND TYPE = 'product'
    ";

    protected $isDeletedQuery = "
        SELECT * FROM
          makaira_connect_deletions
        WHERE
          OXID = :oxid
          AND TYPE = 'product'
        LIMIT 1
    ";

    public function __construct(DatabaseInterface $database, array $modifiers = array())
    {
        $this->database = $database;
        foreach ($modifiers as $modifier) {
            $this->addModifier($modifier);
        }
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
                $product = new Product($row);
                $product = $this->applyModifiers($product, $this->database);
                $change->data = $product;
            }
            $changes[] = $change;
        }

        return new Changes(array(
            'type' => 'product',
            'since' => $since,
            'count' => count($changes),
            'changes' => $changes,
        ));
    }

    /**
     * Mark an object as updated.
     * @param string $oxid
     * @codeCoverageIgnore
     */
    public function touch($oxid)
    {
        $this->database->query($this->touchQuery, ['oxid' => $oxid]);
        $this->database->query($this->undeleteQuery, ['oxid' => $oxid]);
    }

    /**
     * Mark an object as deleted.
     * @param string $oxid
     * @codeCoverageIgnore
     */
    public function delete($oxid)
    {
        $this->database->query($this->touchQuery, ['oxid' => $oxid]);
        $this->database->query($this->deleteQuery, ['oxid' => $oxid]);
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
