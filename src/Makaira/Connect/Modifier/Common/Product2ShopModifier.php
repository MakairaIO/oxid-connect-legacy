<?php

namespace Makaira\Connect\Modifier\Common;

use Makaira\Connect\DatabaseInterface;
use Makaira\Connect\Modifier;
use Makaira\Connect\Type;

class Product2ShopModifier extends Modifier
{
    private $isMultiShop = false;

    const SHOP_FIELD_SET_SIZE = 64;

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

    private function getArrayFromBitmask($bitmask)
    {
        $retArray = array();
        for ($i = 0; $i < self::SHOP_FIELD_SET_SIZE; $i++) {
            if (($bitmask >> $i) & 1) {
                $retArray[] = $i + 1;
            }
        }
        return $retArray;
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
            if (empty($product->OXMAPID)) {
                $bitmask = $product->OXSHOPINCL;
                $product->shop = $this->getArrayFromBitmask($bitmask);
            } else {
                $product->shop = $this->database->query($this->selectQuery, ['mapId' => $product->OXMAPID]);
                $product->shop = array_map(
                    function ($x) {
                        return $x['OXSHOPID'];
                    },
                    $product->shop
                );
            }
        } else {
            $product->shop = [$product->OXSHOPID];
        }
        return $product;
    }
}
