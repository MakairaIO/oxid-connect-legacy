<?php

namespace Makaira\Connect\Modifier\Category;

use Makaira\Connect\DatabaseInterface;
use Makaira\Connect\Modifier;
use Makaira\Connect\Type;
use Makaira\Connect\Type\Category\AssignedOxObject;

class OxObjectModifier extends Modifier
{
    protected $selectQuery = "
      SELECT
        oxobject2category.oxobjectid AS `oxid`,
        oxobject2category.oxpos AS `oxpos`
      FROM
        oxobject2category
      WHERE
        oxobject2category.oxcatnid = :categoryId
    ";

    /**
     * @var DatabaseInterface
     */
    private $database;

    public function __construct(DatabaseInterface $database)
    {
        $this->database = $database;
    }

    /**
     * Modify product and return modified product
     *
     * @param Category $category
     *
     * @return Category
     */
    public function apply(Type $category)
    {
        $objects = $this->database->query($this->selectQuery, ['categoryId' => $category->id]);
        if (!empty($objects)) {
            foreach ($objects as $object) {
                $category->oxobject[] = new AssignedOxObject($object);
            }
        }

        return $category;
    }
}
