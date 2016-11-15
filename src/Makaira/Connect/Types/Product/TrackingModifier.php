<?php
/**
 * Created by PhpStorm.
 * User: benjamin
 * Date: 15.11.16
 * Time: 13:43
 */

namespace Makaira\Connect\Types\Product;


use Makaira\Connect\DatabaseInterface;
use Makaira\Connect\Types\Common\BaseProduct;
use Makaira\Connect\Types\Common\Modifier;

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
     * @param DatabaseInterface $database
     * @return BaseProduct
     */
    public function apply(BaseProduct $product, DatabaseInterface $database)
    {
        $product->TRACKING = $this->tracking->get('product', $product->id);
        $product->OXRATINGCNT = $product->TRACKING['rated'];
        $product->MARM_OXSEARCH_BASKETCOUNT = $product->TRACKING['basketed'];
        $product->MARM_OXSEARCH_REQCOUNT = $product->TRACKING['requested'];
        $product->OXSOLDAMOUNT = $product->TRACKING['sold'];
        return $product;
    }
}
