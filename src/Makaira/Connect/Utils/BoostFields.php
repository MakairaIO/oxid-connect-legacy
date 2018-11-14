<?php
/**
 * This file is part of a marmalade GmbH project
 * It is not Open Source and may not be redistributed.
 * For contact information please visit http://www.marmalade.de
 * Version:    1.0
 * Author:     Jens Richter <richter@marmalade.de>
 * Author URI: http://www.marmalade.de
 */

namespace Makaira\Connect\Utils;

use Makaira\Connect\DatabaseInterface;

class BoostFields
{
    /**
     * @var DatabaseInterface
     */
    private $database;

    /**
     * @var array
     */
    private $minMaxValues;

    /**
     * BoostFieldStatistics constructor.
     *
     * @param DatabaseInterface $database
     */
    public function __construct(DatabaseInterface $database)
    {
        $this->database = $database;
    }

    /**
     * @return array
     */
    public function getMinMaxValues()
    {
        if (null === $this->minMaxValues) {
            $result             = $this->database->query($this->getMinMaxQuery());
            $this->minMaxValues = reset($result);
        }

        return $this->minMaxValues;
    }

    /**
     * @param       $value
     * @param       $key
     * @param float $maxInfluence
     *
     * @return float|int
     */
    public function normalize($value, $key, $maxInfluence = 1.0)
    {
        $minMaxValues = $this->getMinMaxValues();
        $min = $minMaxValues["{$key}_min"];
        if ($min < 0) {
            $max = $this->scaleValue($minMaxValues["{$key}_max"] - $min);
            $scaled = $this->scaleValue($value - $min);
            $min = 0;
        } else {
            $min = $this->scaleValue($minMaxValues["{$key}_min"]);
            $max = $this->scaleValue($minMaxValues["{$key}_max"]);
            $scaled = $this->scaleValue($value);
        }


        $diff = $max - $min;
        $normed = ($diff > 0) ? (($scaled - $min) / $diff) : 0;

        return $maxInfluence * $normed;
    }

    /**
     * @param string $value
     * @param string $key
     * @param float  $maxInfluence
     *
     * @return float|int
     */
    public function normalizeTimestamp($value, $key, $maxInfluence = 1.0)
    {
        $minMaxValues         = $this->getMinMaxValues();
        $max                  = $minMaxValues["{$key}_max"];

        $timestamp            = new \DateTime($value);
        $maxTimestamp         = new \DateTime($max);
        $daysFromMaxTimestamp = (int) $maxTimestamp->diff($timestamp)->format('%r%a');

        $alpha = 0.1;
        $x     = 60;

        // (0.5*(1+alpha*(x+x_zero)/(1+alpha*abs((x+x_zero))))+1/(2*(1+alpha*x_zero)))*max_influence
        return (0.5 * (1 + $alpha * ($daysFromMaxTimestamp + $x) / (1 + $alpha * abs($x + $daysFromMaxTimestamp))) +
                1 / (2 * (1 + $alpha * $x))) * $maxInfluence;
    }

    /**
     * @param $value
     *
     * @return float
     */
    private function scaleValue($value)
    {
        return log($value + 1);
    }

    /**
     * @return string
     */
    private function getMinMaxQuery()
    {
        return '
            SELECT
                MIN(OXSOLDAMOUNT) AS sold_min,
                MAX(OXSOLDAMOUNT) AS sold_max,
                MIN(OXRATING) AS rating_min,
                MAX(OXRATING) AS rating_max,
                MIN(OXVARMINPRICE) AS price_min,
                MAX(OXVARMAXPRICE) AS price_max,
                MAX(OXINSERT) AS insert_max,
                MIN(OXSOLDAMOUNT * OXVARMINPRICE) AS revenue_min,
                MAX(OXSOLDAMOUNT * OXVARMAXPRICE) AS revenue_max,
                MIN(IF(0=OXBPRICE,0,OXVARMINPRICE - OXBPRICE)) AS profit_margin_min,
                MAX(IF(0=OXBPRICE,0,OXVARMAXPRICE - OXBPRICE)) AS profit_margin_max
            FROM
                `oxarticles`
        ';
    }
}
