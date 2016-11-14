<?php

namespace Makaira\Connect\Change\Product;


use Makaira\Connect\Change\Common\Attribute;
use Makaira\Connect\Database;

/**
 * Class AttributeModifier
 * @package Makaira\Connect\Change\Product
 */
class AttributeModifier
{
    private $selectAttributesQuery = "
                        ( SELECT
                            :productActive as `active`,
                            oxattribute.oxid as `oxid`,
                            oxattribute.oxtitle as `oxtitle`,
                            oxobject2attribute.oxpos as `oxpos`,
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
                            oxobject2attribute.oxpos as `oxpos`,
                            oxobject2attribute.oxvalue as `oxvalue`
                        FROM
                            oxarticles
                            JOIN oxobject2attribute ON (oxarticles.oxid = oxobject2attribute.oxobjectid)
                            JOIN oxattribute ON oxobject2attribute.oxattrid = oxattribute.oxid
                        WHERE
                            oxarticles.oxparentid = :productId)
                        ";

    /**
     * Modify product and return modified product
     *
     * @param LegacyProduct $product
     * @param Database $database
     * @return LegacyProduct
     */
    public function apply(LegacyProduct $product, Database $database)
    {
        $attributes = $database->query($this->selectAttributesQuery, [
            'productActive' => $product->OXACTIVE,
            'productId' => $product->id
        ]);
        $product->attribute = array_map(function($row) {
            return new Attribute($row);
        }, $attributes);

        return $product;
    }
}
