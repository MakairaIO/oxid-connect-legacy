<?php

namespace Makaira\Connect\Type\Variant;

use Makaira\Connect\Type\Common\BaseProduct;

class Variant extends BaseProduct
{
    public $parent;

    public $variantactive = 1;
    public $activeto = null;
    public $suggest = [];
    public $activefrom = null;
    public $MARM_OXSEARCH_BASKETCOUNT = 0;
    public $MARM_OXSEARCH_REQCOUNT = 0;
    public $MARM_OXSEARCH_MANUFACTURERTITLE;
    public $category = [];
    public $url;
    public $TRACKING = null;
}
