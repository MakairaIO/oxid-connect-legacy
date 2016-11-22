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

    // @TODO: Move to Makaira\Connect\Repository
    protected $touchQuery = "
        INSERT INTO
          makaira_connect_changes
        (OXID, TYPE, CHANGED)
          VALUES
        (:oxid, 'product', NOW());
    ";

    // @TODO: Move to Makaira\Connect\Repository
    protected $deleteQuery = "
        REPLACE INTO
          makaira_connect_deletions
        (OXID, TYPE, CHANGED)
          VALUES
        (:oxid, 'product', NOW())
    ";

    // @TODO: Move to Makaira\Connect\Repository
    protected $undeleteQuery = "
        DELETE FROM
          makaira_connect_deletions
        WHERE
          OXID = :oxid
          AND TYPE = 'product'
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
     * Mark an object as updated.
     *
     * @param string $oxid
     *
     * @codeCoverageIgnore
     */
    // @TODO: Move to Makaira\Connect\Repository
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
    // @TODO: Move to Makaira\Connect\Repository
    public function delete($oxid)
    {
        $this->database->execute($this->touchQuery, ['oxid' => $oxid]);
        $this->database->execute($this->deleteQuery, ['oxid' => $oxid]);
    }
}
