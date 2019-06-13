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
        $onStock = true;

        if (\oxRegistry::getConfig()->getShopConfVar('blUseStock')) {
            $oxArticle = \oxRegistry::get('oxArticle');
            $table = $oxArticle->getCoreTableName();
            $stockSnippet = "(oxarticles.oxstockflag != 2 OR oxarticles.oxstock > 0 OR oxarticles.oxvarstock > 0)";

            // 1 --> Standard
            // 2 --> Wenn ausverkauft offline
            // 3 --> Wenn ausverkauft nicht bestellbar
            // 4 --> Fremdlager

            $sql = "SELECT * FROM {$table} WHERE OXID = '{$product->id}' AND {$stockSnippet}";
            $result = $this->database->query($sql);

            $onStock = (bool) count($result);
        }

        $product->mak_onstock = $onStock;

        return $product;
    }
}
