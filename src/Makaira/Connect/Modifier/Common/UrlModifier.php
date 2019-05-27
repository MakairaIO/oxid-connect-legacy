<?php
/**
 * This file is part of a marmalade GmbH project
 * It is not Open Source and may not be redistributed.
 * For contact information please visit http://www.marmalade.de
 * Version:    1.0
 * Author:     Jens Richter <richter@marmalade.de>
 * Author URI: http://www.marmalade.de
 */

namespace Makaira\Connect\Modifier\Common;

use function get_class;
use Makaira\Connect\Modifier;
use Makaira\Connect\Type;

class UrlModifier extends Modifier
{
    /**
     * @var string
     */
    private $objectClass;
    /**
     * @var \oxSeoEncoder|\oxSeoEncoderArticle|\oxSeoEncoderCategory|\oxSeoEncoderManufacturer
     */
    private $encoder;
    /**
     * @var \oxLang
     */
    private $oxLang;

    /**
     * UrlModifier constructor.
     *
     * @param string                                     $objectClass
     * @param \oxSeoEncoder|\oxSeoEncoderArticle|\oxSeoEncoderCategory|\oxSeoEncoderManufacturer $encoder
     * @param \oxLang                                                                            $oxLang
     */
    public function __construct($objectClass, \oxSeoEncoder $encoder, \oxLang $oxLang)
    {
        $this->objectClass = $objectClass;
        $this->encoder = $encoder;
        $this->oxLang = $oxLang;
    }

    /**
     * Modify product and return modified product
     *
     * @param Type $type
     *
     * @return Type
     * @throws \oxSystemComponentException
     */
    public function apply(Type $type)
    {
        $objectData = array_merge((array) $type, $type->additionalData);

        unset(
            $objectData['active'],
            $objectData['activefrom'],
            $objectData['activeto'],
            $objectData['additionalData'],
            $objectData['attribute'],
            $objectData['category'],
            $objectData['id'],
            $objectData['mak_active'],
            $objectData['shop'],
            $objectData['suggest'],
            $objectData['timestamp'],
            $objectData['url'],
            $objectData['variantactive']
        );

        /** @var \oxBase $object */
        $object = new $this->objectClass;
        $object->assign($objectData);

        if ($object instanceof \oxArticle) {
            $urlGetter = 'getManufacturerUri';
        } elseif ($object instanceof \oxCategory) {
            $urlGetter = 'getCategoryUri';
        } elseif ($object instanceof \oxManufacturer) {
            $urlGetter = 'getManufacturerUri';
        }
        $type->url = $this->encoder->$urlGetter($object, $this->oxLang->getBaseLanguage());
        $type->selfLinks = $this->getSelfLinks($object, $urlGetter);

        return $type;
    }

    private function getSelfLinks($object, $urlGetter)
    {
        $selfLinks = array();
        $languageIds = $this->oxLang->getLanguageIds();
        foreach (array_keys($languageIds) as $id) {
            $key = $languageIds[$id];
            $selfLinks[$key] = $this->encoder->$urlGetter($object, $id);
        }
        return $selfLinks;
    }
}
