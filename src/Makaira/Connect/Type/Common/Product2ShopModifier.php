<?php

namespace Makaira\Connect\Type\Common;


use Makaira\Connect\DatabaseInterface;
use Makaira\Connect\Type\ChangeDatum;

class Product2ShopModifier extends Modifier
{

    private   $isMultiShop = false;
    protected $selectQuery = '
        SELECT
          OXSHOPID
        FROM
          oxarticles2shop
        WHERE
          OXMAPOBJECTID = :mapId;
    ';

    /**
     * Product2ShopModifier constructor.
     * @param bool $isMall
     */
    public function __construct($isMultiShop)
    {
        $this->isMultiShop = $isMultiShop;
    }

    /**
     * Modify product and return modified product
     *
     * @param BaseProduct $product
     * @param DatabaseInterface $database
     * @return BaseProduct
     */
    public function apply(ChangeDatum $product, DatabaseInterface $database)
    {
        if ($this->isMultiShop) {
            $product->shop = $database->query($this->selectQuery, ['mapId' => $product->OXMAPID]);
            $product->shop = array_map(function($x) { return $x['OXSHOPID']; }, $product->shop);
        } else {
            $product->shop = [$product->OXSHOPID];
        }
        return $product;
    }
}
