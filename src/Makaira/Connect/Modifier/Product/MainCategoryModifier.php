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

    /**
     * @var \oxSeoEncoder|\oxSeoEncoderArticle|\oxSeoEncoderCategory|\oxSeoEncoderManufacturer
     */
    private $encoder;

    /**
     * @var \oxLang
     */
    private $oxLang;

    public function __construct(DatabaseInterface $database, \oxSeoEncoder $encoder, \oxLang $oxLang)
    {
        $this->database = $database;
        $this->encoder  = $encoder;
        $this->oxLang   = $oxLang;
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
        if (!isset($type->maincategory)) {
            $sql = "SELECT OXCATNID FROM oxobject2category WHERE OXOBJECTID=:productId ORDER BY OXTIME LIMIT 1";

            $result = $this->database->query($sql, ['productId' => $type->OXID]);
            if (0 == count($result)) {
                $result = $this->database->query($sql, ['productId' => $type->OXPARENTID]);
            }
            if (count($result) > 0 && isset($result[0]['OXCATNID'])) {
                $categoryId         = $result[0]['OXCATNID'];
                $type->maincategory = $categoryId;
                $oCategory          = oxNew('oxcategory');
                $languageId         = $this->oxLang->getBaseLanguage();
                $oCategory->loadInLang($languageId, $categoryId);
                $type->maincategoryurl = $this->encoder->getCategoryUri($oCategory, $languageId);
            }
        }

        return $type;
    }
}
