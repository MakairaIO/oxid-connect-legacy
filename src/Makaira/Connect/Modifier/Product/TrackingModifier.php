<?php

namespace Makaira\Connect\Modifier\Product;

use Makaira\Connect\Type\Common\BaseProduct;
use Makaira\Connect\Type;
use Makaira\Connect\Modifier;

class TrackingModifier extends Modifier
{
    /** @var  \AbstractOxSearchTracking */
    private $tracking;

    /**
     * TrackingModifier constructor.
     * @param \AbstractOxSearchTracking $tracking
     */
    public function __construct(\AbstractOxSearchTracking $tracking)
    {
        $this->tracking = $tracking;
    }

    /**
     * Modify product and return modified product
     *
     * @param BaseProduct $product
     * @return BaseProduct
     */
    public function apply(Type $product)
    {
        $product->TRACKING = $this->tracking->get('product', $product->id);
        $product->OXRATINGCNT = $product->TRACKING['rated'];
        $product->MARM_OXSEARCH_BASKETCOUNT = $product->TRACKING['basketed'];
        $product->MARM_OXSEARCH_REQCOUNT = $product->TRACKING['requested'];
        $product->OXSOLDAMOUNT = $product->TRACKING['sold'];
        return $product;
    }
}
