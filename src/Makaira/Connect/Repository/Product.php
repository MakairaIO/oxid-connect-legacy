<?php

namespace Makaira\Connect\Repository;

use Makaira\Connect\Database;
use Makaira\Connect\Result\Changes;
use Makaira\Connect\Change;

class Product
{
    /**
     * @var Database
     */
    private $database;

    private $modifiers;

    protected $productSelectQuery = "
        SELECT
            makaira_connect_product_changes.sequence,
            oxarticles.oxid AS `id`,
            UNIX_TIMESTAMP(oxarticles.oxtimestamp) AS `timestamp`,
            oxarticles.*,
            oxartextends.oxlongdesc,
            oxartextends.oxtags,
            oxmanufacturers.oxtitle AS MARM_OXSEARCH_MANUFACTURERTITLE
        FROM
            makaira_connect_product_changes
            LEFT JOIN oxarticles ON oxarticles.oxid = makaira_connect_product_changes.oxid
            LEFT JOIN oxartextends ON oxarticles.oxid = oxartextends.oxid
            LEFT JOIN oxmanufacturers ON oxarticles.oxmanufacturerid = oxmanufacturers.oxid
        WHERE
            oxparentid = \"\"
            AND sequence > :since
		ORDER BY
			sequence ASC
        LIMIT :limit";

    public function __construct(Database $database, array $modifiers = array())
    {
        $this->database = $database;
        $this->modifiers = $modifiers;
    }

    public function addModifier(Change\Product\Modifier $modifier)
    {
        $this->modifiers[] = $modifier;
    }

    public function getChangesSince($since, $limit = 50)
    {
        $result = $this->database->query($this->productSelectQuery, ['since' => $since, 'limit' => $limit]);

        $changes = array();
        foreach ($result as $row) {
            $change = new Change();
            $change->sequence = $row['sequence'];
            unset($row['sequence']);

            // @TODO: Do we want to pass the full product / changes list to
            // themodifier to allow aggregated queries?
            $product = new Change\LegacyProduct($row);
            foreach ($this->modifiers as $modifier) {
                $product = $modifier->apply($product);
            }
            $change->data = $product;
            $changes[] = $change;
        }

        return new Changes(array(
            'since' => $since,
            'count' => count($changes),
            'changes' => $changes,
        ));
    }
}
