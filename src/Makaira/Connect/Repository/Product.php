<?php

namespace Makaira\Connect\Repository;

use Makaira\Connect\DatabaseInterface;
use Makaira\Connect\Result\Changes;
use Makaira\Connect\Change;

class Product
{
    /**
     * @var DatabaseInterface
     */
    private $database;

    /**
     * @var Change\Product\Modifier[]
     */
    private $modifiers = [];

    protected $productSelectQuery = "
        SELECT
            makaira_connect_product_changes.sequence,
            oxarticles.oxid AS `id`,
            UNIX_TIMESTAMP(oxarticles.oxtimestamp) AS `timestamp`,
            oxarticles.*,
            oxartextends.oxlongdesc as `OXLONGDESC`,
            oxartextends.oxtags as `OXTAGS`,
            oxmanufacturers.oxtitle AS MARM_OXSEARCH_MANUFACTURERTITLE
        FROM
            makaira_connect_product_changes
            LEFT JOIN oxarticles ON oxarticles.oxid = makaira_connect_product_changes.oxid
            LEFT JOIN oxartextends ON oxarticles.oxid = oxartextends.oxid
            LEFT JOIN oxmanufacturers ON oxarticles.oxmanufacturerid = oxmanufacturers.oxid
        WHERE
            oxparentid = ''
            AND sequence > :since
        ORDER BY
            sequence ASC
        LIMIT :limit";

    protected $productTouchQuery = "
        INSERT INTO
          makaira_connect_product_changes
        (OXID, CHANGED)
          VALUES
        (:oxid, NOW());
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
     * @codeCoverageIgnore
     * @param Change\Product\Modifier $modifier
     */
    public function addModifier(Change\Product\Modifier $modifier)
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
            $product = new Change\Product\LegacyProduct($row);
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
        $this->database->query($this->productTouchQuery, ['oxid' => $oxid]);
    }
}
