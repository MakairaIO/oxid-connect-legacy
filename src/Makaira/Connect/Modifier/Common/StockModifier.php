<?php

namespace Makaira\Connect\Modifier\Common;

use Makaira\Connect\Modifier;
use Makaira\Connect\Type;
use Makaira\Connect\Type\Product\Product;

class StockModifier extends Modifier
{
    private $database;

    public function __construct($database)
    {
        $this->database = $database;
    }

    /**
     * Modify product and return modified product
     *
     * @param Product $product
     *
     * @return BaseProduct
     */
    public function apply(Type $product)
    {
        $stockFlag = 1;
        $stock     = 1;
        $onStock   = true;

        if (
        \oxRegistry::getConfig()
            ->getShopConfVar('blUseStock')
        ) {
            if (!isset($product->OXSTOCKFLAG) || !isset($product->OXSTOCK) || !isset($product->OXVARSTOCK)) {
                $oxArticle = \oxRegistry::get('oxArticle');
                $table     = $oxArticle->getCoreTableName();
                $sql       =
                    "SELECT OXPARENTID, OXSTOCKFLAG, OXSTOCK, OXVARSTOCK FROM {$table} WHERE OXID = '{$product->id}'";
                $result    = $this->database->query($sql);
                if ($result) {
                    $stockFlag = $result[0]['OXSTOCKFLAG'];
                    $stock     = $result[0]['OXSTOCK'] + $result[0]['OXVARSTOCK'];
                }
            } else {
                $stock = $product->OXSTOCK + $product->OXVARSTOCK;
                $stockFlag = $product->OXSTOCKFLAG;
            }

            // 1 --> Standard
            // 2 --> Wenn ausverkauft offline
            // 3 --> Wenn ausverkauft nicht bestellbar
            // 4 --> Fremdlager
            $onStock = (2 != $stockFlag) || (0 < $stock);
            if (4 === $stockFlag) {
                $stock = 1;
            }
        }

        $product->onstock = $onStock;
        $product->stock   = $stock;

        return $product;
    }
}
    