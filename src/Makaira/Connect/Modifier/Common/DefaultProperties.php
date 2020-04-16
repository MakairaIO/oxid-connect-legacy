<?php

namespace Makaira\Connect\Modifier\Common;

use Makaira\Connect\DatabaseInterface;
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
        'id'               => 'OXID',
        'es_id'            => '',
        'timestamp'        => 'OXTIMESTAMP',
        'url'              => '',
        'active'           => 'OXACTIVE',
        'meta_keywords'    => '',
        'meta_description' => '',
    ];

    private $productFieldMapping = [
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
        'searchable'     => 'OXISSEARCH',
        'ean'            => 'OXARTNUM',
        'stock'          => 1,
        'onstock'        => true,
        'title'          => 'OXTITLE',
    ];

    private $categoryFieldMapping = [
        'sort'           => 'OXSORT',
        'shortdesc'      => 'OXDESC',
        'longdesc'       => 'OXLONGDESC',
        'hidden'         => 'OXHIDDEN',
        'category_title' => 'OXTITLE',

    ];

    private $manufacturerFieldMapping = [
        'shortdesc'          => 'OXSHORTDESC',
        'manufacturer_title' => 'OXTITLE',
    ];

    private $boolFields = [
        'searchable',
        'hidden'
    ];

    public function __construct(DatabaseInterface $database)
    {
        $this->db = $database;
    }

    /**
     * @SuppressWarnings(CyclomaticComplexity)
     */
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

        foreach ($this->boolFields as $boolField) {
            if (isset($entity->$boolField)) {
                $entity->$boolField = (bool) $entity->$boolField;
            }
        }

        return $entity;
    }
}
