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

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function getChangesSince($since, $limit = 50)
    {
        $result = $this->database->query($this->productSelectQuery, ['since' => $since, 'limit' => $limit]);

        return new Changes(array(
            'since' => $since,
            'count' => count($result),
            'changes' => array_map(
                function ($row) {
                    $change = new Change();
                    $change->sequence = $row['sequence'];
                    unset($row['sequence']);

                    $change->data = new Change\Product($row);
                    return $change;
                },
                $result
            ),
        ));
    }
}
