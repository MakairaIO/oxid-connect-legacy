<?php

namespace Makaira\Connect\Types\Product;


use Makaira\Connect\DatabaseInterface;
use Makaira\Connect\Types\Common\BaseProduct;
use Makaira\Connect\Types\Common\Modifier;

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
     * @param BaseProduct $product
     * @param DatabaseInterface $database
     * @return BaseProduct
     */
    public function apply(BaseProduct $product, DatabaseInterface $database)
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
