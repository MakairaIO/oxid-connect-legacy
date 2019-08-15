<?php

namespace Makaira\Connect\Type\Manufacturer;

use Makaira\Connect\Type;

/**
 * This file is part of a marmalade GmbH project
 * It is not Open Source and may not be redistributed.
 * For contact information please visit http://www.marmalade.de
 * Version:    1.0
 * Author:     Jens Richter <richter@marmalade.de>
 * Author URI: http://www.marmalade.de
 */
class Manufacturer extends Type
{
    public $mak_manufacturerId;
    public $manufacturer_title	;
    public $shortdesc;
    public $mak_meta_keywords;
    public $mak_meta_description;
    public $selfLinks = [];

    //    public $OXACTIVE;
    //    public $OXSHOPID;
    //    public $OXTITLE;
    //    public $OXSHORTDESC;
}
