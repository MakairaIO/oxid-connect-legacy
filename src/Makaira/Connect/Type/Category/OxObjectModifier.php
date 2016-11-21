<?php

namespace Makaira\Connect\Type\Category;


use Makaira\Connect\DatabaseInterface;
use Makaira\Connect\Type\Common\ChangeDatum;
use Makaira\Connect\Type\Common\Modifier;

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
     * Modify product and return modified product
     *
     * @param Category $category
     * @param DatabaseInterface $database
     * @return Category
     */
    public function apply(ChangeDatum $category, DatabaseInterface $database)
    {
        $objects = $database->query($this->selectQuery, ['categoryId' => $category->id]);
        if (!empty($objects)) {
            foreach ($objects as $object) {
                $category->oxobject[] = new AssignedOxObject($object);
            }
        }
        return $category;
    }
}
