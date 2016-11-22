<?php

namespace Makaira\Connect\Modifier\Product;

use Makaira\Connect\DatabaseInterface;
use Makaira\Connect\Type\Common\BaseProduct;
use Makaira\Connect\Type;
use Makaira\Connect\Modifier;

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
    public function apply(Type $product, DatabaseInterface $database)
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
