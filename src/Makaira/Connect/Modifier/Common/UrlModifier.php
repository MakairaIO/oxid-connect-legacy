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

use Makaira\Connect\Modifier;
use Makaira\Connect\Type;

class UrlModifier extends Modifier
{
    private $object;
    private $encoder;

    /**
     * UrlModifier constructor.
     *
     * @param \oxBase|\oxArticle|\oxCategory|\oxManufacturer                                     $object
     * @param \oxSeoEncoder|\oxSeoEncoderArticle|\oxSeoEncoderCategory|\oxSeoEncoderManufacturer $encoder
     */
    public function __construct(\oxBase $object, \oxSeoEncoder $encoder, \oxLang $oxLang)
    {
        $this->object  = $object;
        $this->encoder = $encoder;
        $this->oxLang = $oxLang;
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
        $this->object->load($type->OXID);

        if ($this->object instanceof \oxArticle) {
            $type->url = $this->encoder->getArticleMainUri($this->object, $this->oxLang->getBaseLanguage());
        } elseif ($this->object instanceof \oxCategory) {
            $type->url = $this->encoder->getCategoryUri($this->object, $this->oxLang->getBaseLanguage());
        } elseif ($this->object instanceof \oxManufacturer) {
            $type->url = $this->encoder->getManufacturerUri($this->object, $this->oxLang->getBaseLanguage());
        }

        return $type;
    }
}
