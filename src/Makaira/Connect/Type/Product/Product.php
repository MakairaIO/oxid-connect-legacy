<?php

namespace Makaira\Connect\Type\Product;

use Makaira\Connect\Type\Common\BaseProduct;

class Product extends BaseProduct
{
    public $isVariant = false;
    public $activeto;
    public $activefrom;
    public $variantactive = 1;
    public $suggest = [];
    public $category = [];
    public $TRACKING;
    public $MARM_OXSEARCH_BASKETCOUNT = 0;
    public $MARM_OXSEARCH_REQCOUNT = 0;
    public $MARM_OXSEARCH_MANUFACTURERTITLE = '';
}
