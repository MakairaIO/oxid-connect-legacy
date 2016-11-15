<?php

namespace Makaira\Connect\Change\Product;


use Makaira\Connect\Change\Common\Category;
use Makaira\Connect\DatabaseInterface;

class CategoryModifier extends Modifier
{
    private $deepInheritance = false;

    private $selectCategoriesQuery = "
                        SELECT
                            oxcatnid AS catid,
                            oxpos AS oxpos,
                            oxshopid AS shopid
                        FROM
                            oxobject2category
                        WHERE
                            oxobject2category.oxobjectid = :productId
                            AND :productActive = :productActive
                        ";

    private $selectLeftRightRootQuery = "
                        SELECT
                          oxrootid,
                          oxleft,
                          oxright
                        FROM
                          oxcategories
                        WHERE
                          oxid = :catid
                        ";

    private $selectDeepCategoriesQuery = "
                        SELECT
                          oxid AS catid,
                          9999 AS oxpos,
                          :shopid AS shopid
                        FROM
                          oxcategories
                        WHERE
                          OXROOTID = :oxrootid
                          AND OXLEFT <= :oxleft
                          AND OXRIGHT >= :oxright;
                         ";

    /**
     * CategoryModifier constructor.
     * @param bool $deepInheritance
     */
    public function __construct($deepInheritance)
    {
        $this->deepInheritance = $deepInheritance;
    }

    /**
     * Modify product and return modified product
     *
     * @param LegacyProduct $product
     * @param DatabaseInterface $database
     * @return LegacyProduct
     */
    public function apply(LegacyProduct $product, DatabaseInterface $database)
    {
        $categories = $database->query(
            $this->selectCategoriesQuery,
            [
                'productId' => $product->id,
                'productActive' => $product->OXACTIVE,
            ]
        );
        $categories = array_map(
            function ($cat) {
                return new Category($cat);
            }, $categories
        );
        if ($this->deepInheritance) {
            /** @var Category $category */
            foreach ($categories as $category) {
                $leftRightRoot = $database->query($this->selectLeftRightRootQuery, ['catid' => $category->catid]);
                if (!empty($leftRightRoot)) {
                    $leftRightRoot = reset($leftRightRoot);
                    $leftRightRoot['shopid'] = $category->shopid;
                    $categoryPath = $database->query($this->selectDeepCategoriesQuery, $leftRightRoot);
                    foreach ($categoryPath as $cat) {
                        $categories[] = new Category($cat);
                    }
                }
            }
        }
        $product->category = $categories;
        return $product;
    }
}
