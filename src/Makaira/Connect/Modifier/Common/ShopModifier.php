<?php

namespace Makaira\Connect\Modifier\Common;

use Makaira\Connect\DatabaseInterface;
use Makaira\Connect\Modifier;
use Makaira\Connect\Type;

class ShopModifier extends Modifier
{
    private $isMultiShop = false;

    const SHOP_FIELD_SET_SIZE = 64;

    protected $selectQuery = '
        SELECT
          OXSHOPID
        FROM
          %s
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
    public function __construct(DatabaseInterface $database, $isMultiShop, $mappingTable)
    {
        $this->database    = $database;
        $this->isMultiShop = $isMultiShop;
        $this->selectQuery = sprintf($this->selectQuery, $mappingTable);
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
     * @param Type $product
     *
     * @return Type
     */
    public function apply(Type $type)
    {
        if ($this->isMultiShop) {
            if (empty($type->OXMAPID)) {
                $bitmask    = $type->OXSHOPINCL;
                $type->shop = $this->getArrayFromBitmask($bitmask);
            } else {
                $type->shop = $this->database->query(
                    $this->selectQuery,
                    ['mapId' => $type->OXMAPID]
                );
                $type->shop = array_map(
                    function ($x) {
                        return $x['OXSHOPID'];
                    },
                    $type->shop
                );
            }
        } else {
            $type->shop = [$type->OXSHOPID];
        }

        return $type;
    }
}
