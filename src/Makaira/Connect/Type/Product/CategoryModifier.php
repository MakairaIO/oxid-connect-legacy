<?php

namespace Makaira\Connect\Type\Product;


use Makaira\Connect\DatabaseInterface;
use Makaira\Connect\Type\ChangeDatum;
use Makaira\Connect\Type\Common\BaseProduct;
use Makaira\Connect\Type\Common\AssignedCategory;
use Makaira\Connect\Type\Common\Modifier;

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
     * @param BaseProduct $product
     * @param DatabaseInterface $database
     * @return BaseProduct
     */
    public function apply(ChangeDatum $product, DatabaseInterface $database)
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
                return new AssignedCategory($cat);
            }, $categories
        );
        if ($this->deepInheritance) {
            /** @var AssignedCategory $category */
            foreach ($categories as $category) {
                $leftRightRoot = $database->query($this->selectLeftRightRootQuery, ['catid' => $category->catid]);
                if (!empty($leftRightRoot)) {
                    $leftRightRoot = reset($leftRightRoot);
                    $leftRightRoot['shopid'] = $category->shopid;
                    $categoryPath = $database->query($this->selectDeepCategoriesQuery, $leftRightRoot);
                    foreach ($categoryPath as $cat) {
                        $categories[] = new AssignedCategory($cat);
                    }
                }
            }
        }
        $product->category = $categories;
        return $product;
    }
}
