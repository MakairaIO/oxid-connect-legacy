<?php

namespace Makaira\Connect\Modifier\Common;

use Makaira\Connect\DatabaseInterface;
use Makaira\Connect\Modifier;
use Makaira\Connect\Type;
use Makaira\Connect\Type\Common\AssignedAttribute;
use Makaira\Connect\Type\Common\AssignedTypedAttribute;
use Makaira\Connect\Type\Common\BaseProduct;

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
                            oxattribute.oxid as `id`,
                            oxattribute.oxtitle as `title`,
                            oxobject2attribute.oxvalue as `value`
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

    private $selectVariantsQuery = "
                        SELECT 
                            1 as `active`, 
                            parent.oxvarname as `title`, 
                            variant.oxvarselect as `value`
                        FROM 
                            oxarticles parent
                            JOIN oxarticles variant ON parent.oxid = variant.oxparentid
                        WHERE parent.oxactive = 1
                            AND parent.oxhidden = 0
                            AND variant.oxparentid = :productId
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
        $this->database       = $database;
        $this->activeSnippet  = $activeSnippet;
        $this->attributeInt   = array_unique($attributeInt);
        $this->attributeFloat = array_unique($attributeFloat);
    }

    /**
     * Modify product and return modified product
     *
     * @param BaseProduct|Type $product
     *
     * @return BaseProduct|Type
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

        $product->attribute      = [];
        $product->attributeInt   = [];
        $product->attributeFloat = [];
        foreach ($attributes as $attributeData) {
            $product->attribute[] = new AssignedAttribute([
                'active'  => $attributeData['active'],
                'oxid'    => $attributeData['id'],
                'oxtitle' => $attributeData['title'],
                'oxvalue' => $attributeData['value'],
            ]);

            $attribute               = new AssignedTypedAttribute($attributeData);
            $product->attributeStr[] = $attribute;

            $attributeId = $attributeData['id'];
            if (in_array($attributeId, $this->attributeInt)) {
                $product->attributeInt[] = $attribute;
            }
            if (in_array($attributeId, $this->attributeFloat)) {
                $product->attributeFloat[] = $attribute;
            }
        }

        $variants = $this->database->query(
            $this->selectVariantsQuery,
            [
                'productId' => $product->id,
            ]
        );

        foreach ($variants as $variantData) {
            $titleArray = array_map('trim', explode('|', $variantData['title']));
            $valueArray = array_map('trim', explode('|', $variantData['value']));

            foreach ($titleArray as $index => $title) {
                $title                = "{$title} (VARSELECT)";
                $product->attribute[] = new AssignedAttribute([
                    'active'  => $variantData['active'],
                    'oxid'    => md5($title),
                    'oxtitle' => $title,
                    'oxvalue' => $valueArray[$index],
                ]);

                $product->attributeStr[] = new AssignedTypedAttribute([
                    'active' => $variantData['active'],
                    'id'     => md5($title),
                    'title'  => $title,
                    'value'  => $valueArray[$index],
                ]);
            }
        }

        return $product;
    }
}
