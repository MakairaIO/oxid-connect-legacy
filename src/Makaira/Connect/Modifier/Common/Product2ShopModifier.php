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

    protected $shopTableQuery = 'SHOW TABLES LIKE "oxarticles2shop"';

    private function getArrayFromBitmask($bitmask) {
        $retArray = array();
        for ($i = 0; $i < 64; $i++) {
            if (($bitmask >> $i) & 1) {
                $retArray[] = $i + 1;
            }
        }
        return $retArray;
    }

    private function hasTable() {
        $hasTable = $this->database->query($this->shopTableQuery);
        return empty($hasTable);
    }

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
            if ($this->hasTable()) {
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
