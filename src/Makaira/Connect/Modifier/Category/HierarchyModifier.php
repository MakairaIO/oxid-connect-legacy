<?php
/**
 * This file is part of a marmalade GmbH project
 * It is not Open Source and may not be redistributed.
 * For contact information please visit http://www.marmalade.de
 * Version:    1.0
 * Author:     Jens Richter <richter@marmalade.de>
 * Author URI: http://www.marmalade.de
 */

namespace Makaira\Connect\Modifier\Category;

use Makaira\Connect\DatabaseInterface;
use Makaira\Connect\Modifier;
use Makaira\Connect\Type;
use Makaira\Connect\Type\Category\Category;

class HierarchyModifier extends Modifier
{
    protected $selectQuery = "
      SELECT
        oc.OXID
      FROM
        oxcategories oc
      WHERE
        oc.OXLEFT <= :left 
        AND oc.OXRIGHT >= :right 
        AND oc.OXROOTID = :rootId
      ORDER BY oc.OXLEFT;
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
        $path = $this->database->query(
            $this->selectQuery,
            [
                'left'   => $category->OXLEFT,
                'right'  => $category->OXRIGHT,
                'rootId' => $category->OXROOTID,
            ]
        );

        $hierarchy = array_map(
            function ($value) {
                return $value['OXID'];
            },
            $path
        );

        $category->depth     = count($hierarchy);
        $category->hierarchy = implode('//', $hierarchy);

        return $category;
    }
}
