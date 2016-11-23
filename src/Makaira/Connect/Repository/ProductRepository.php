<?php

namespace Makaira\Connect\Repository;

use Makaira\Connect\Change;
use Makaira\Connect\DatabaseInterface;
use Makaira\Connect\RepositoryInterface;
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
     * @var ModifierList
     */
    private $modifiers;

    protected $selectQuery = "
        SELECT
            UNIX_TIMESTAMP(oxarticles.oxtimestamp) AS `timestamp`,
            oxarticles.*,
            oxartextends.oxlongdesc as `OXLONGDESC`,
            oxartextends.oxtags as `OXTAGS`,
            oxmanufacturers.oxtitle AS MARM_OXSEARCH_MANUFACTURERTITLE
        FROM
            oxarticles
            LEFT JOIN oxartextends ON oxarticles.oxid = oxartextends.oxid
            LEFT JOIN oxmanufacturers ON oxarticles.oxmanufacturerid = oxmanufacturers.oxid
        WHERE
            oxarticles.oxid = :id
            AND oxarticles.oxparentid = ''
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

        $product = new Product(reset($result));
        $product = $this->modifiers->applyModifiers($product, $this->database);
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
}
