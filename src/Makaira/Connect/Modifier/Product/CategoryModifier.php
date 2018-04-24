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
                            oxcatnid AS catid,
                            oxpos AS oxpos,
                            oxshopid AS shopid
                        FROM
                            oxobject2category
                        WHERE
                            oxobject2category.oxobjectid = :productId
                            AND :productActive = :productActive
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
        $categories = $this->database->query(
            $this->selectCategoriesQuery,
            [
                'productId'     => $product->id,
                'productActive' => $product->OXACTIVE,
            ]
        );
        $categories = array_map(
            function ($cat) {
                return new AssignedCategory($cat);
            },
            $categories
        );

        $product->category = $categories;

        return $product;
    }
}
