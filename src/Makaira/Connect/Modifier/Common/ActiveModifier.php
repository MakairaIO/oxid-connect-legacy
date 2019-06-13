<?php

namespace Makaira\Connect\Modifier\Common;

use Makaira\Connect\Modifier;
use Makaira\Connect\Type;
use Makaira\Connect\Type\Product\Product;

class ActiveModifier extends Modifier
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
        $activeSnippet = '';
        $table = '';

        switch ($this->getDocType()) {
            case "product":
            case "variant":
                $oxArticle = \oxRegistry::get('oxArticle');
                $activeSnippet = $oxArticle->getSqlActiveSnippet(true);
                $table = $oxArticle->getCoreTableName();
                break;
            case "category":
                $oxCategory = \oxRegistry::get('oxCategory');
                $activeSnippet = $oxCategory->getSqlActiveSnippet(true);
                $table = $oxCategory->getCoreTableName();
                break;
            case "manufacturer":
                $oxManufacturer = \oxRegistry::get('oxManufacturer');
                $activeSnippet = $oxManufacturer->getSqlActiveSnippet(true);
                $table = $oxManufacturer->getCoreTableName();
                break;
        }

        $sql = "SELECT * FROM {$table} WHERE OXID = '{$product->id}' AND {$activeSnippet}";
        $result = $this->database->query($sql);

        $product->mak_active = (bool) count($result);

        // FIXME: We can only remove this legacy logic once the query is using mak_active and all customers
        // have updated their module (?)
        $product->active = (bool) $product->OXACTIVE;
        if (isset($product->OXHIDDEN)) {
            $product->active = $product->active && !((bool) $product->OXHIDDEN);
        }

        return $product;
    }
}
