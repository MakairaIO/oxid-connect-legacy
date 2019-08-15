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
        'searchkeys'     => 'OXSEARCHKEYS',
        'hidden'         => 'OXHIDDEN',
        'sort'           => 'OXSORT',
        'shortdesc'      => 'OXSHORTDESC',
        'longdesc'       => 'OXLONGDESC',
        'manufacturerid' => 'OXMANUFACTURERID',
        'price'          => 'OXPRICE',
        'insert'         => 'OXINSERT',
        'soldamount'     => 'OXSOLDAMOUNT',
        'rating'         => 'OXRATING',
        'searchable'       => 'OXISSEARCH',

        'ean'     => 'OXARTNUM',
        'stock'   => 1,
        'onstock' => true,
    ];

    private $categoryFieldMapping = [
        'category_title' => 'OXTITLE',
        'sort'           => 'OXSORT',
        'shortdesc'      => 'OXDESC',
        'longdesc'       => 'OXLONGDESC',
        'hidden'         => 'OXHIDDEN',

    ];

    private $manufacturerFieldMapping = [
        'manufacturer_title' => 'OXTITLE',
        'shortdesc'          => 'OXSHORTDESC',
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
