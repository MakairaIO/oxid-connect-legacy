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
        if (\oxRegistry::getConfig()->getShopConfVar('blUseStock')) {
            $oxArticle = \oxRegistry::get('oxArticle');
            $table = $oxArticle->getCoreTableName();
            $stockSnippet = "(oxarticles.oxstockflag = 4 OR oxarticles.oxstock > 0 OR oxarticles.oxvarstock > 0)";

            $sql = "SELECT * FROM {$table} WHERE OXID = '{$product->id}' AND {$stockSnippet}";
            $result = $this->database->query($sql);

            $product->mak_onstock = (bool) count($result);
        }

        return $product;
    }
}
