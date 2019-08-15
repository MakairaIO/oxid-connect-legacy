<?php

namespace Makaira\Connect\Type\Product;

use Makaira\Connect\Type\Common\BaseProduct;

class Product extends BaseProduct
{
    public $parent = '';

    public $mak_meta_keywords;
    public $mak_meta_description;
    public $selfLinks = [];
    public $mainCategory;
    public $mainCategoryUrl;

    public $mak_boost_norm_insert = 0.0;
    public $mak_boost_norm_sold = 0.0;
    public $mak_boost_norm_rating = 0.0;
    public $mak_boost_norm_revenue = 0.0;
    public $mak_boost_norm_profit_margin = 0.0;

    public $isVariant = false;
    public $activeto;
    public $activefrom;
    public $suggest = [];
    public $category = [];
    public $TRACKING;
}
