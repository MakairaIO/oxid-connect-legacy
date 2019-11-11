<?php

namespace Makaira\Connect\Modifier\Common;

use Makaira\Connect\DatabaseInterface;
use Makaira\Connect\Modifier;
use Makaira\Connect\Type;
use Makaira\Connect\Type\Common\AssignedTypedAttribute;
use Makaira\Connect\Type\Common\BaseProduct;
use Makaira\Connect\Exception as ConnectException;

/**
 * Class AttributeModifier
 *
 * @package Makaira\Connect\Type\ProductRepository
 * @SuppressWarnings(PHPMD)
 */
class AttributeModifier extends Modifier
{
    private $selectAttributesQuery = '
                        SELECT
                            oxattribute.oxid as `id`,
                            oxattribute.oxtitle as `title`,
                            oxobject2attribute.oxvalue as `value`
                        FROM
                            oxobject2attribute
                            JOIN oxattribute ON oxobject2attribute.oxattrid = oxattribute.oxid
                        WHERE
                            oxobject2attribute.oxobjectid = :productId
                            AND oxobject2attribute.oxvalue != \'\'
                        ';

    private $selectVariantsQuery = '
                        SELECT
                            parent.oxvarname as `title`,
                            variant.oxvarselect as `value`
                        FROM
                            oxarticles parent
                            JOIN oxarticles variant ON parent.oxid = variant.oxparentid
                        WHERE
                            variant.oxparentid = :productId
                        ';

    private $selectVariantsAttributesQuery = '
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
                            AND {{activeSnippet}}
                        ';

    private $selectVariantQuery = '
                        SELECT
                            parent.oxvarname as `title`,
                            variant.oxvarselect as `value`
                        FROM
                            oxarticles parent
                            JOIN oxarticles variant ON parent.oxid = variant.oxparentid
                        WHERE
                            variant.oxid = :productId
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
            throw new ConnectException("Cannot fetch attributes without a product ID.");
        }

        $attributes = $this->database->query(
            $this->selectAttributesQuery,
            [
                'productId' => $product->id,
            ]
        );

        if (false === $product->isVariant) {
            $product->tmpAttributeStr   = [];
            $product->tmpAttributeInt   = [];
            $product->tmpAttributeFloat = [];
            foreach ($attributes as $attributeData) {
                $attribute                = new AssignedTypedAttribute($attributeData);
                $product->tmpAttributeStr[] = $attribute;

                $attributeId = $attributeData['id'];
                if (in_array($attributeId, $this->attributeInt)) {
                    $product->tmpAttributeInt[] = $attribute;
                }
                if (in_array($attributeId, $this->attributeFloat)) {
                    $product->tmpAttributeFloat[] = $attribute;
                }
            }
            $product->attributeStr   = $product->tmpAttributeStr;
            $product->attributeInt   = $product->tmpAttributeInt;
            $product->attributeFloat = $product->tmpAttributeFloat;
            $query                   =
                str_replace('{{activeSnippet}}', $this->activeSnippet, $this->selectVariantsAttributesQuery);
            $_attributes             = $this->database->query(
                $query,
                [
                    'productId' => $product->id,
                ]
            );
            $attributes              = [];
            foreach ($_attributes as $attributeData) {
                $id                = $attributeData['id'] . $attributeData['value'];
                $attributes[ $id ] = $attributeData;
            }
        } else {
            $product->attributeStr   = [];
            $product->attributeInt   = [];
            $product->attributeFloat = [];
        }

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

        if (false === $product->isVariant) {
            $variants = $this->database->query(
                $this->selectVariantsQuery,
                [
                    'productId' => $product->id,
                ]
            );
        } else {
            $variants = $this->database->query(
                $this->selectVariantQuery,
                [
                    'productId' => $product->id,
                ]
            );
        }

        if ($variants) {
            $hashArray = array_map('md5', array_map('trim', explode('|', $variants[0]['title'])));

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
                            'id'    => $hashTitle,
                            'title' => $title,
                            'value' => $value,
                        ]
                    );
                }
            }
        }

        return $product;
    }
}
