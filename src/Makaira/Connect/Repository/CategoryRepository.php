<?php

namespace Makaira\Connect\Repository;

use Makaira\Connect\Change;
use Makaira\Connect\DatabaseInterface;
use Makaira\Connect\RepositoryInterface;
use Makaira\Connect\Type\Category\Category;

class CategoryRepository implements RepositoryInterface
{
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
    protected $allIdsQuery = "
      SELECT
       OXID
      FROM
       oxcategories;
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
        $this->database  = $database;
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
        $category     = new Category($result[0]);
        $category     = $this->modifiers->applyModifiers($category, $this->database);
        $change->data = $category;

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
        return 'category';
    }

    /**
     * Get all IDs handled by this repository.
     *
     * @return string[]
     */
    public function getAllIds()
    {
        $result = $this->database->query($this->allIdsQuery);
        return array_map(function($r) { return $r['OXID']; }, $result);
    }
}
