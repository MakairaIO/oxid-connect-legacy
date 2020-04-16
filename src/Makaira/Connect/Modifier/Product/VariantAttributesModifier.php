<?php

namespace Makaira\Connect\Modifier\Product;

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
 */
class VariantAttributesModifier extends Modifier
{
    private $selectVariantNameQuery = '
                        SELECT
                            oxvarname
                        FROM
                            oxarticles
                        WHERE
                            oxid = :productId
                        ';

    private $selectVariantDataQuery = '
                        SELECT
                            oxid as `id`,
                            oxvarselect as `value`
                        FROM
                            oxarticles
                        WHERE
                            oxparentid = :productId
                            AND {{activeSnippet}}
                        ';

    private $selectVariantAttributesQuery = '
                        SELECT
                            oxattribute.oxid as `id`,
                            oxobject2attribute.oxvalue as `value`
                        FROM
                            oxobject2attribute
                            JOIN oxattribute ON oxobject2attribute.oxattrid = oxattribute.oxid
                        WHERE
                            oxobject2attribute.oxvalue != \'\'
                            AND oxobject2attribute.oxobjectid in (:productId, :variantId)
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

    /**
     * @param DatabaseInterface $database
     * @param string $activeSnippet
     * @param array $attributeInt
     * @param array $attributeFloat
     */
    public function __construct(
        DatabaseInterface $database,
        $activeSnippet,
        $attributeInt,
        $attributeFloat
    ) {
        $this->database       = $database;
        $this->activeSnippet  = $activeSnippet;
        $this->attributeInt   = array_unique((array) $attributeInt);
        $this->attributeFloat = array_unique((array) $attributeFloat);
    }

    /**
     * Modify product and return modified product
     *
     * @param BaseProduct|Type $product
     *
     * @return BaseProduct|Type
     * @SuppressWarnings(CyclomaticComplexity)
     */
    public function apply(Type $product)
    {
        if (!$product->id) {
            throw new ConnectException("Cannot fetch attributes without a product ID.");
        }

        $product->attributes = [];

        $variantName = $this->database->query(
            $this->selectVariantNameQuery,
            [
                'productId' => $product->id,
            ],
            false
        );
        $single      = ($variantName[0]['oxvarname'] === '');

        if (!$single) {
            $titleArray = array_map('trim', explode('|', $variantName[0]['oxvarname']));
            $hashArray  = array_map('md5', $titleArray);

            $query    = str_replace('{{activeSnippet}}', $this->activeSnippet, $this->selectVariantDataQuery);
            $variants = $this->database->query(
                $query,
                [
                    'productId' => $product->id,
                ]
            );
        } else {
            $variants = [['id' => '']];
        }

        foreach ($variants as $variant) {
            $id = $variant['id'];
            if ($id) {
                $valueArray        = array_map('trim', explode('|', $variant['value']));
                $variantAttributes = [];

                foreach ($hashArray as $index => $hash) {
                    if (in_array($hash, $this->attributeInt)) {
                        $variantAttributes[ $hash ] = (int) $valueArray[ $index ];
                    } elseif (in_array($hash, $this->attributeFloat)) {
                        $variantAttributes[ $hash ] = (float) $valueArray[ $index ];
                    } else {
                        $variantAttributes[ $hash ] = (string) $valueArray[ $index ];
                    }
                }
            }

            $attributes = $this->database->query(
                $this->selectVariantAttributesQuery,
                [
                    'productId' => $product->id,
                    'variantId' => $id,
                ]
            );

            foreach ($attributes as $attribute) {
                $hash  = $attribute['id'];
                $value = $attribute['value'];

                if (in_array($hash, $this->attributeInt)) {
                    $variantAttributes[ $hash ] = (int) $value;
                } elseif (in_array($hash, $this->attributeFloat)) {
                    $variantAttributes[ $hash ] = (float) $value;
                } else {
                    $variantAttributes[ $hash ] = (string) $value;
                }
            }

            if ($variantAttributes) {
                $product->attributes[] = $variantAttributes;
            }
        }

        return $product;
    }
}
