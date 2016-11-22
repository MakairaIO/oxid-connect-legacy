<?php

namespace Makaira\Connect\Modifier\Common;

use Makaira\Connect\DatabaseInterface;
use Makaira\Connect\Modifier;
use Makaira\Connect\Type;

class Product2ShopModifier extends Modifier
{
    private $isMultiShop = false;
    protected $selectQuery = '
        SELECT
          OXSHOPID
        FROM
          oxarticles2shop
        WHERE
          OXMAPOBJECTID = :mapId;
    ';

    /**
     * @var DatabaseInterface
     */
    private $database;

    /**
     * @param DatabaseInterface $database
     * @param bool              $isMultiShop
     */
    public function __construct(DatabaseInterface $database, $isMultiShop)
    {
        $this->database    = $database;
        $this->isMultiShop = $isMultiShop;
    }

    /**
     * Modify product and return modified product
     *
     * @param BaseProduct $product
     *
     * @return BaseProduct
     */
    public function apply(Type $product)
    {
        if ($this->isMultiShop) {
            $product->shop = $this->database->query($this->selectQuery, ['mapId' => $product->OXMAPID]);
            $product->shop = array_map(
                function ($x) {
                    return $x['OXSHOPID'];
                },
                $product->shop
            );
        } else {
            $product->shop = [$product->OXSHOPID];
        }

        return $product;
    }
}
