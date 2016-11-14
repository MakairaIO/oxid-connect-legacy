<?php
/**
 * Created by PhpStorm.
 * User: benjamin
 * Date: 14.11.16
 * Time: 16:45
 */

namespace Makaira\Connect\Change\Product;


use Makaira\Connect\Database;

class SuggestModifier extends Modifier
{
    private $suggestFields = [];

    /**
     * SuggestModifier constructor.
     * @param array $suggestFields
     */
    public function __construct(array $suggestFields)
    {
        $this->suggestFields = $suggestFields;
    }

    /**
     * Modify product and return modified product
     *
     * @param LegacyProduct $product
     * @param Database $database
     * @return LegacyProduct
     */
    public function apply(LegacyProduct $product, Database $database)
    {
        $suggest = [];
        foreach ($this->suggestFields as $suggestField) {
            $suggest[] = $product->$suggestField;
        }
        $suggest = explode(',', join(',', $suggest));
        $suggest = array_unique(array_map('trim', $suggest));
        $product->suggest = $suggest;

        return $product;
    }
}
