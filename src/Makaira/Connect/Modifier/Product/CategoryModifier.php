<?php

namespace Makaira\Connect\Modifier\Product;

use Makaira\Connect\DatabaseInterface;
use Makaira\Connect\Modifier;
use Makaira\Connect\Type;
use Makaira\Connect\Type\Common\AssignedCategory;
use Makaira\Connect\Type\Common\BaseProduct;

class CategoryModifier extends Modifier
{
    private $selectCategoriesQuery = "
                        SELECT
                            o2c.oxcatnid AS catid,
                            o2c.oxpos AS oxpos,
                            o2c.oxshopid AS shopid,
                            oc.OXTITLE as title,
                            oc.OXACTIVE AS active,
                            oc.OXLEFT AS oxleft,
                            oc.OXRIGHT AS oxright,
                            oc.OXROOTID AS oxrootid
                        FROM
                            oxobject2category o2c
                        LEFT JOIN oxcategories oc ON
                            o2c.oxcatnid = oc.oxid
                        WHERE
                            o2c.oxobjectid = :productId
                            AND :productActive = :productActive
                        ";

    protected $selectCategoryPathQuery = "
      SELECT
        oc.OXTITLE as title,
        oc.OXACTIVE as active
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

    /**
     * @param DatabaseInterface $database
     */
    public function __construct(DatabaseInterface $database)
    {
        $this->database        = $database;
    }

    /**
     * Modify product and return modified product
     *
     * @param BaseProduct $product
     *
     * @return BaseProduct
     */
    public function apply(Type $product)
    {
        $allCats = $this->database->query(
            $this->selectCategoriesQuery,
            [
                'productId'     => $product->id,
                'productActive' => $product->active,
            ]
        );

        $categories = [];

        foreach ($allCats as $cat) {
            $catPaths = $this->database->query(
                $this->selectCategoryPathQuery,
                [
                    'left'      => $cat['oxleft'],
                    'right'     => $cat['oxright'],
                    'rootId'    => $cat['oxrootid'],
                ]
            );

            $path  = '';
            $active = true;
            foreach ($catPaths as $catPath) {
                $active &= $catPath['active'];
                if (!$active) {
                    break;
                }
                $path .= $catPath['title'] . '/';
            }

            if ($active) {
                $categories[] = new AssignedCategory(
                    [
                        'catid'  => $cat['catid'],
                        'title'  => $cat['title'],
                        'shopid' => $cat['shopid'],
                        'pos'    => $cat['oxpos'],
                        'path'   => $path,
                    ]
                );
            }
        }

        $product->category = $categories;

        return $product;
    }
}
