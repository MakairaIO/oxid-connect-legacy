<?php
/**
 * Created by PhpStorm.
 * User: benjamin
 * Date: 15.11.16
 * Time: 13:43
 */

namespace Makaira\Connect\Change\Product;


use Makaira\Connect\DatabaseInterface;

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
     * @param LegacyProduct $product
     * @param DatabaseInterface $database
     * @return LegacyProduct
     */
    public function apply(LegacyProduct $product, DatabaseInterface $database)
    {
        $product->TRACKING = $this->tracking->get('product', $product->id);
        $product->OXRATINGCNT = $product->TRACKING['rated'];
        $product->MARM_OXSEARCH_BASKETCOUNT = $product->TRACKING['basketed'];
        $product->MARM_OXSEARCH_REQCOUNT = $product->TRACKING['requested'];
        $product->OXSOLDAMOUNT = $product->TRACKING['sold'];
        return $product;
    }
}
