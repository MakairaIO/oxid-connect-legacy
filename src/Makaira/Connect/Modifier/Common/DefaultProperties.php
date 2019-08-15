<?php

namespace Makaira\Connect\Modifier\Common;

use Makaira\Connect\Modifier;
use Makaira\Connect\Type;

//use Makaira\Connect\Type\Common\BaseProduct;
//use Makaira\Connect\Type\Product\Product;
//use Makaira\Connect\Type\Variant\Variant;
//use Makaira\Connect\Type\Category\Category;
//use Makaira\Connect\Type\Manufacturer\Manufacturer;

class DefaultProperties extends Modifier
{
    private $db;

    private $commonFieldMapping = [
        'id'                   => 'OXID',
        'es_id'                => '',
        'timestamp'            => 'OXTIMESTAMP',
        'url'                  => '',
        'active'           => 'OXACTIVE',
        'mak_meta_keywords'    => '',
        'mak_meta_description' => '',
    ];

    private $productFieldMapping = [
        'title'          => 'OXTITLE',
        'mak_searchkeys'     => 'OXSEARCHKEYS',
        'mak_hidden'         => 'OXHIDDEN',
        'mak_sort'           => 'OXSORT',
        'mak_shortdesc'      => 'OXSHORTDESC',
        'mak_longdesc'       => 'OXLONGDESC',
        'mak_manufacturerId' => 'OXMANUFACTURERID',
        'mak_price'          => 'OXPRICE',
        'mak_insert'         => 'OXINSERT',
        'mak_soldamount'     => 'OXSOLDAMOUNT',
        'mak_rating'         => 'OXRATING',
        'mak_issearch'       => 'OXISSEARCH',

        'mak_ean'     => 'OXARTNUM',
        'mak_stock'   => 1,
        'mak_onstock' => true,
    ];

    private $categoryFieldMapping = [
        'mak_category_title' => 'OXTITLE',
        'mak_sort'           => 'OXSORT',
        'mak_shortdesc'      => 'OXDESC',
        'mak_longdesc'       => 'OXLONGDESC',
        'mak_hidden'         => 'OXHIDDEN',

    ];

    private $manufacturerFieldMapping = [
        'mak_manufacturer_title' => 'OXTITLE',
        'mak_shortdesc'          => 'OXSHORTDESC',
    ];

    public function __construct($database)
    {
        $this->db = $database;
    }

    public function apply(Type $entity)
    {
        $mappingFields = [];

        switch ($this->getDocType()) {
            case "product":
            case "variant":
                $mappingFields = $this->productFieldMapping;
                break;

            case "category":
                $mappingFields = $this->categoryFieldMapping;
                break;

            case "manufacturer":
                $mappingFields = $this->manufacturerFieldMapping;
                break;

            default:

                break;
        }

        $mappingFields = array_merge($this->commonFieldMapping, $mappingFields);

        foreach ($mappingFields as $target => $source) {
            if ($source && isset($entity->$source)) {
                $entity->$target = $entity->$source;
            } elseif ($target && !isset($entity->$target)) {
                $entity->$target = $source;
            }
        }

        return $entity;
    }
}
