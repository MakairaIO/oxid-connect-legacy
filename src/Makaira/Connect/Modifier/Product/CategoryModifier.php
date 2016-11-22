<?php

namespace Makaira\Connect\Modifier\Product;

use Makaira\Connect\DatabaseInterface;
use Makaira\Connect\Type\Common\BaseProduct;
use Makaira\Connect\Type\Common\AssignedCategory;
use Makaira\Connect\Type;
use Makaira\Connect\Modifier;

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
     * @var DatabaseInterface
     */
    private $database;

    /**
     * @param DatabaseInterface $database
     * @param bool $deepInheritance
     */
    public function __construct(DatabaseInterface $database, $deepInheritance)
    {
        $this->database = $database;
        $this->deepInheritance = $deepInheritance;
    }

    /**
     * Modify product and return modified product
     *
     * @param BaseProduct $product
     * @return BaseProduct
     */
    public function apply(Type $product)
    {
        $categories = $this->database->query(
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
                $leftRightRoot = $this->database->query($this->selectLeftRightRootQuery, ['catid' => $category->catid]);
                if (!empty($leftRightRoot)) {
                    $leftRightRoot = reset($leftRightRoot);
                    $leftRightRoot['shopid'] = $category->shopid;
                    $categoryPath = $this->database->query($this->selectDeepCategoriesQuery, $leftRightRoot);
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
