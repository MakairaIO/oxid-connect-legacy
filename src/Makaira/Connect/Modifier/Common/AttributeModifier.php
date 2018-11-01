<?php

namespace Makaira\Connect\Modifier\Common;

use Makaira\Connect\DatabaseInterface;
use Makaira\Connect\Modifier;
use Makaira\Connect\Type;
use Makaira\Connect\Type\Common\AssignedAttribute;

/**
 * Class AttributeModifier
 *
 * @package Makaira\Connect\Type\ProductRepository
 */
class AttributeModifier extends Modifier
{
    private $selectAttributesQuery = "
                        ( SELECT
                            :productActive as `active`,
                            oxattribute.oxid as `oxid`,
                            oxattribute.oxtitle as `oxtitle`,
                            oxobject2attribute.oxvalue as `oxvalue`
                        FROM
                            oxobject2attribute
                            JOIN oxattribute ON oxobject2attribute.oxattrid = oxattribute.oxid
                        WHERE
                            oxobject2attribute.oxobjectid = :productId
                        ) UNION (
                        SELECT
                            oxactive as `active`,
                            oxattribute.oxid as `oxid`,
                            oxattribute.oxtitle as `oxtitle`,
                            oxobject2attribute.oxvalue as `oxvalue`
                        FROM
                            oxarticles
                            JOIN oxobject2attribute ON (oxarticles.oxid = oxobject2attribute.oxobjectid)
                            JOIN oxattribute ON oxobject2attribute.oxattrid = oxattribute.oxid
                        WHERE
                            oxarticles.oxparentid = :productId
                            AND {{activeSnippet}})
                        ";

    /**
     * @var DatabaseInterface
     */
    private $database;

    /**
     * @var string
     */
    private $activeSnippet;

    /**
     * @var array
     */
    private $attributeInt = [];

    /**
     * @var array
     */
    private $attributeFloat = [];

    public function __construct(
        DatabaseInterface $database,
        $activeSnippet,
        array $attributeInt,
        array $attributeFloat
    ) {
        $this->database      = $database;
        $this->activeSnippet = $activeSnippet;
        $this->attributeInt = $attributeInt;
        $this->attributeFloat = $attributeFloat;
    }

    /**
     * Modify product and return modified product
     *
     * @param Type $product
     *
     * @return Type
     */
    public function apply(Type $product)
    {
        if (!$product->id) {
            throw new \RuntimeException("Cannot fetch attributes without a product ID.");
        }

        $query      = str_replace('{{activeSnippet}}', $this->activeSnippet, $this->selectAttributesQuery);
        $attributes = $this->database->query(
            $query,
            [
                'productActive' => $product->OXACTIVE,
                'productId'     => $product->id,
            ]
        );

        $product->attribute = array_map(
            function ($row) {
                return new AssignedAttribute($row);
            },
            $attributes
        );

        $product->attributeInt   = [];
        $product->attributeFloat = [];
        foreach ($attributes as $attribute) {
            $attributeId = $attribute['oxid'];
            if (in_array($attributeId, $this->attributeFloat)) {
                $attribute['oxvalue']      = (float) $attribute['oxvalue'];
                $product->attributeFloat[] = new AssignedAttribute($attribute);
            }
            if (in_array($attributeId, $this->attributeInt)) {
                $attribute['oxvalue']    = (int) $attribute['oxvalue'];
                $product->attributeInt[] = new AssignedAttribute($attribute);
            }
        }

        return $product;
    }
}
