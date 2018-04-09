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

use Makaira\Connect\DatabaseInterface;
use Makaira\Connect\Modifier;
use Makaira\Connect\Type;

class MainCategoryModifier extends Modifier
{
    /**
     * @var DatabaseInterface
     */
    private $database;

    public function __construct(DatabaseInterface $database)
    {
        $this->database = $database;
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
        // skip database request if field is already set
        if (!isset($type->mainCategory)) {
            $sql = "SELECT OXCATNID FROM oxobject2category WHERE OXOBJECTID=:productId ORDER BY OXTIME LIMIT 1";

            $result             = $this->database->query($sql, ['productId' => $type->OXID]);
            if (count($result) > 0 && isset($result[0]['OXCATNID'])) {
                $type->mainCategory = $result[0]['OXCATNID'];
            }
        }

        return $type;
    }
}
