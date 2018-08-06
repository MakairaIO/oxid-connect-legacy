<?php
/**
 * This file is part of a marmalade GmbH project
 * It is not Open Source and may not be redistributed.
 * For contact information please visit http://www.marmalade.de
 * Version:    1.0
 * Author:     Jens Richter <richter@marmalade.de>
 * Author URI: http://www.marmalade.de
 */

namespace Makaira\Connect\Modifier\Product;

use Makaira\Connect\Modifier;
use Makaira\Connect\Type;
use Makaira\Connect\Utils\BoostFields;

class BoostFieldModifier extends Modifier
{
    /**
     * @var BoostFields
     */
    private $boostFieldUtilities;

    /**
     * BoostFieldModifier constructor.
     *
     * @param BoostFields $boostFieldUtilities
     */
    public function __construct(BoostFields $boostFieldUtilities)
    {
        $this->boostFieldUtilities = $boostFieldUtilities;
    }

    /**
     * Modify product and return modified product
     *
     * @param Type $type
     *
     * @return Type
     */
    public function apply(Type $type)
    {
        $type->mak_boost_norm_insert = $this->boostFieldUtilities->normalizeTimestamp(
            $type->OXINSERT,
            'insert'
        );

        $type->mak_boost_norm_sold = $this->boostFieldUtilities->normalize(
            $type->OXSOLDAMOUNT,
            'sold'
        );
        $type->mak_boost_norm_rating = $this->boostFieldUtilities->normalize(
            $type->OXRATING,
            'rating'
        );

        $priceAverage = ($type->OXVARMINPRICE + $type->OXVARMAXPRICE)/2;
        $type->mak_boost_norm_revenue = $this->boostFieldUtilities->normalize(
            $priceAverage * $type->OXSOLDAMOUNT,
            'revenue'
        );

        $type->mak_boost_norm_profit_margin = $this->boostFieldUtilities->normalize(
            (0 == round($type->OXBPRICE)) ? 0 : ($priceAverage - $type->OXBPRICE),
            'profit_margin'
        );
        return $type;
    }
}
