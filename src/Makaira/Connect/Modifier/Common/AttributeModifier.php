<?php

namespace Makaira\Connect\Modifier\Common;

use Makaira\Connect\DatabaseInterface;
use Makaira\Connect\Modifier;
use Makaira\Connect\Type;
use Makaira\Connect\Type\Common\AssignedTypedAttribute;
use Makaira\Connect\Type\Common\BaseProduct;

/**
 * Class AttributeModifier
 *
 * @package Makaira\Connect\Type\ProductRepository
 */
class AttributeModifier extends Modifier
{
    private $selectAttributesQuery = '
                        ( SELECT
                            oxattribute.oxid as `id`,
                            oxattribute.oxtitle as `title`,
                            oxobject2attribute.oxvalue as `value`
                        FROM
                            oxobject2attribute
                            JOIN oxattribute ON oxobject2attribute.oxattrid = oxattribute.oxid
                        WHERE
                            oxobject2attribute.oxobjectid = :productId
                            AND oxobject2attribute.oxvalue != \'\'
                        ) UNION (
                        SELECT
                            oxattribute.oxid as `id`,
                            oxattribute.oxtitle as `title`,
                            oxobject2attribute.oxvalue as `value`
                        FROM
                            oxarticles
                            JOIN oxobject2attribute ON (oxarticles.oxid = oxobject2attribute.oxobjectid)
                            JOIN oxattribute ON oxobject2attribute.oxattrid = oxattribute.oxid
                        WHERE
                            oxarticles.oxparentid = :productId
                            AND oxobject2attribute.oxvalue != \'\'
                            AND {{activeSnippet}})
                        ';

    private $selectVariantsQuery = '
                        SELECT
                            parent.oxvarname as `title`,
                            variant.oxvarselect as `value`
                        FROM
                            oxarticles parent
                            JOIN oxarticles variant ON parent.oxid = variant.oxparentid
                        WHERE variant.oxparentid = :productId
                        ';

    private $selectVariantNameQuery = '
                        SELECT
                            oxvarname
                        FROM
                            oxarticles
                        WHERE
                            oxid = :productId
                        ';

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

        $product->attributeInt   = [];
        $product->attributeFloat = [];
        foreach ($attributes as $attributeData) {
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

        $variantName = $this->database->query(
            $this->selectVariantNameQuery,
            [
                'productId' => $product->id,
            ],
            false
        );
        $hashArray   = array_map('md5', array_map('trim', explode('|', $variantName[0]["oxvarname"])));

        $allVariants = [];
        foreach ($variants as $variantData) {
            $titleArray = array_map('trim', explode('|', $variantData['title']));
            $valueArray = array_map('trim', explode('|', $variantData['value']));

            foreach ($titleArray as $index => $title) {
                $title                         = "{$title}  (VarSelect)";
                $allVariants[ $title ][]       = $valueArray[ $index ];
                $allVariants[ $title ]["hash"] = $hashArray[ $index ];
            }
        }

        foreach ($allVariants as $title => $values) {
            $hashTitle = $values["hash"];
            unset($values["hash"]);

            $uniqueValues = array_unique($values);

            foreach ($uniqueValues as $value) {
                $product->attributeStr[] = new AssignedTypedAttribute(
                    [
                        'id'     => $hashTitle,
                        'title'  => $title,
                        'value'  => $value,
                    ]
                );
            }
        }

        return $product;
    }
}
