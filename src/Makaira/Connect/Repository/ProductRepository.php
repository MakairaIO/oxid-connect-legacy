<?php

namespace Makaira\Connect\Repository;

use Makaira\Connect\Change;
use Makaira\Connect\DatabaseInterface;
use Makaira\Connect\Result\Changes;
use Makaira\Connect\Type\Common\Modifier;
use Makaira\Connect\Type\Product\Product;

class ProductRepository implements RepositoryInterface
{
    /**
     * @var DatabaseInterface
     */
    private $database;

    /**
     * @var Modifier[]
     */
    private $modifiers = [];

    protected $productSelectQuery = "
        SELECT
            makaira_connect_changes.sequence,
            oxarticles.oxid AS `id`,
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
            oxarticles.oxparentid = ''
            AND makaira_connect_changes.sequence > :since
            AND makaira_connect_changes.type = 'product'
        ORDER BY
            sequence ASC
        LIMIT :limit";

    protected $touchQuery = "
        INSERT INTO
          makaira_connect_changes
        (OXID, TYPE, CHANGED)
          VALUES
        (:oxid, 'product', NOW());
    ";

    public function __construct(DatabaseInterface $database, array $modifiers = array())
    {
        $this->database = $database;
        foreach ($modifiers as $modifier) {
            $this->addModifier($modifier);
        }
    }

    /**
     * Add a modifier.
     * @param Modifier $modifier
     */
    public function addModifier(Modifier $modifier)
    {
        $this->modifiers[] = $modifier;
    }

    /**
     * Fetch and serialize changes.
     * @param int $since Sequence offset
     * @param int $limit Fetch limit
     * @return Changes
     */
    public function getChangesSince($since, $limit = 50)
    {
        $result = $this->database->query($this->productSelectQuery, ['since' => $since, 'limit' => $limit]);

        $changes = array();
        foreach ($result as $row) {
            $change = new Change();
            $change->id = $row['id'];
            $change->sequence = $row['sequence'];
            unset($row['sequence']);

            // @TODO: Do we want to pass the full product / changes list to the modifier to allow aggregated queries?
            $product = new Product($row);
            foreach ($this->modifiers as $modifier) {
                $product = $modifier->apply($product, $this->database);
            }
            $change->data = $product;
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
     */
    public function touch($oxid)
    {
        $this->database->query($this->touchQuery, ['oxid' => $oxid]);
    }
}
