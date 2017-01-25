<?php

namespace Makaira\Connect\Repository;

use Makaira\Connect\Change;
use Makaira\Connect\DatabaseInterface;
use Makaira\Connect\RepositoryInterface;
use Makaira\Connect\Type\Common\Modifier;
use Makaira\Connect\Type\Product\Product;

class ProductRepository implements RepositoryInterface
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
            oxarticles.OXID as `id`,
            UNIX_TIMESTAMP(oxarticles.oxtimestamp) AS `timestamp`,
            oxarticles.*,
            oxartextends.oxlongdesc AS `OXLONGDESC`,
            oxartextends.oxtags AS `OXTAGS`,
            oxmanufacturers.oxtitle AS MARM_OXSEARCH_MANUFACTURERTITLE
        FROM
            oxarticles
            LEFT JOIN oxartextends ON oxarticles.oxid = oxartextends.oxid
            LEFT JOIN oxmanufacturers ON oxarticles.oxmanufacturerid = oxmanufacturers.oxid
        WHERE
            oxarticles.oxid = :id
            AND oxarticles.oxparentid = ''
    ";

    protected $allIdsQuery = "
      SELECT
        OXID
      FROM
        oxarticles
      WHERE
        OXPARENTID = ''
    ";

    public function __construct(DatabaseInterface $database, ModifierList $modifiers)
    {
        $this->database  = $database;
        $this->modifiers = $modifiers;
    }

    public function get($id)
    {
        $result = $this->database->query($this->selectQuery, ['id' => $id]);

        $change = new Change();
        if (!count($result)) {
            $change->deleted = true;

            return $change;
        }

        $product      = new Product(reset($result));
        $product      = $this->modifiers->applyModifiers($product, $this->database);
        $change->data = $product;

        return $change;
    }

    /**
     * Get TYPE of repository.
     *
     * @return string
     * @codeCoverageIgnore
     */
    public function getType()
    {
        return 'product';
    }

    /**
     * Get all IDs handled by this repository.
     *
     * @return string[]
     */
    public function getAllIds()
    {
        $result = $this->database->query($this->allIdsQuery);

        return array_map(
            function ($row) {
                return $row['OXID'];
            },
            $result
        );
    }
}
