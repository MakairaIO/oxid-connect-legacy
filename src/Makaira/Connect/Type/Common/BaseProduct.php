<?php

namespace Makaira\Connect\Type\Common;

use Makaira\Connect\Type;

/**
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class BaseProduct extends Type
{
    /* variant attributes */
    public $attributes = [];

    /* attributes as String */
    public $attributeStr = [];

    /* attributes as Integer */
    public $attributeInt = [];

    /* attributes as Float */
    public $attributeFloat = [];

    /* required fields + mak-fields */
    public $mak_ean = '';
    public $title = '';
    public $searchkeys = '';
    public $hidden = false;
    public $sort = 0;
    public $longdesc = '';
    public $shortdesc = '';
    public $mak_stock = 0;
    public $onstock = false;
    public $mak_manufacturerId = '';
    public $mak_manufacturer_title = '';
    public $mak_price = 0.00;
    public $mak_insert;
    public $mak_soldamount = 0;
    public $mak_rating = 0.0;
    public $mak_issearch = 1;
    public $picture_url = [];

//    /* active */
//    public $OXACTIVE;
//    public $OXACTIVEFROM;
//    public $OXACTIVETO;
//
//    /* identifier */
//    public $OXARTNUM;
//    public $OXEAN;
//    public $OXDISTEAN;
//    public $OXMPN;
//
//    /* manufacturer, vendor */
//    public $OXMANUFACTURERID;
//    public $OXVENDORID;
//
//    /* title and description */
//    public $OXTITLE;
//    public $OXSHORTDESC;
//    public $OXLONGDESC;
//    public $OXTAGS;
//    public $OXSEARCHKEYS;
//
//    /* search */
//    public $OXISSEARCH;
//
//    /* price */
//    public $OXPRICE;
//    public $OXBLFIXEDPRICE;
//    public $OXPRICEA;
//    public $OXPRICEB;
//    public $OXPRICEC;
//    public $OXBPRICE;
//    public $OXTPRICE;
//    public $OXVAT;
//    public $OXVARMINPRICE;
//    public $OXVARMAXPRICE;
//
//    /* multishop */
//    public $OXMAPID;
//    public $OXSHOPID;
//    /* for EE < 5.2 */
//    public $OXSHOPINCL;
//    public $OXSHOPEXCL;
//
//    /* parent-child */
//    public $OXPARENTID;
//    public $OXSORT;
//    public $OXVARNAME;
//    public $OXVARSELECT;
//    public $OXVARCOUNT;
//
//    /* unit price */
//    public $OXUNITNAME;
//    public $OXUNITQUANTITY;
//
//    /* images */
//    public $OXTHUMB;
//    public $OXICON;
//    public $OXPIC1;
//    public $OXPIC2;
//    public $OXPIC3;
//    public $OXPIC4;
//    public $OXPIC5;
//    public $OXPIC6;
//    public $OXPIC7;
//
//    /* dimensions */
//    public $OXWEIGHT;
//    public $OXLENGTH;
//    public $OXWIDTH;
//    public $OXHEIGHT;
//
//    /* stock and delivery */
//    public $OXSTOCK;
//    public $OXVARSTOCK;
//    public $OXSTOCKFLAG;
//    public $OXDELIVERY;
//    public $OXMINDELTIME;
//    public $OXMAXDELTIME;
//    public $OXDELTIMEUNIT;
//    public $OXSTOCKTEXT;
//    public $OXNOSTOCKTEXT;
//
//    /* rating */
//    public $OXRATING;
//    public $OXRATINGCNT;
//
//    /* timestamps */
//    public $OXINSERT;
//    public $OXTIMESTAMP;
//
//    /* digital product */
//    public $OXFILE;
//    public $OXISDOWNLOADABLE;
//    public $OXNONMATERIAL;
//
//    public $OXISCONFIGURABLE;
//    public $OXFREESHIPPING;
//    public $OXSOLDAMOUNT;
//
//    /* Backport from Oxid 6.0 */
//    public $OXHIDDEN;
}
