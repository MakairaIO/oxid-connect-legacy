<?php

namespace Makaira\Connect\Type\Product;

use Makaira\Connect\Type\Common\BaseProduct;

class Product extends BaseProduct
{
    public $variantactive = 1;
    public $activeto = null;
    public $suggest = [];
    public $activefrom = null;
    public $MARM_OXSEARCH_BASKETCOUNT = 0;
    public $MARM_OXSEARCH_REQCOUNT = 0;
    public $MARM_OXSEARCH_MANUFACTURERTITLE;
    public $category = [];
    public $TRACKING = null;
}
