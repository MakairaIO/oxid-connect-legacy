<?php

namespace Makaira\Connect\Type\Common;

use Makaira\Connect\Type;

/**
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class BaseProduct extends Type
{
    /* attributes */
    public $attribute = [];

    /* Added for OXSEARCH */
    public $MARM_OXSEARCH_BOOST;
    public $MARM_OXSEARCH_PROFITMARGIN;

    /* active */
    public $OXACTIVE;
    public $OXACTIVEFROM;
    public $OXACTIVETO;

    /* identifier */
    public $OXARTNUM;
    public $OXEAN;
    public $OXDISTEAN;
    public $OXMPN;

    /* manufacturer, vendor */
    public $OXMANUFACTURERID;
    public $OXVENDORID;

    /* title and description */
    public $OXTITLE;
    public $OXSHORTDESC;
    public $OXLONGDESC;
    public $OXTAGS;
    public $OXSEARCHKEYS;

    /* search */
    public $OXISSEARCH;

    /* price */
    public $OXPRICE;
    public $OXBLFIXEDPRICE;
    public $OXPRICEA;
    public $OXPRICEB;
    public $OXPRICEC;
    public $OXBPRICE;
    public $OXTPRICE;
    public $OXVAT;
    public $OXVARMINPRICE;
    public $OXVARMAXPRICE;
//    public $OXUPDATEPRICE;
//    public $OXUPDATEPRICEA;
//    public $OXUPDATEPRICEB;
//    public $OXUPDATEPRICEC;
//    public $OXUPDATEPRICETIME;

    /* multishop */
    public $OXMAPID;
    public $OXSHOPID;
    /* for EE < 5.2 */
    public $OXSHOPINCL;
    public $OXSHOPEXCL;

    /* parent-child */
    public $OXPARENTID;
    public $OXSORT;
    public $OXVARNAME;
    public $OXVARSELECT;
    public $OXVARCOUNT;

    /* unit price */
    public $OXUNITNAME;
    public $OXUNITQUANTITY;

    /* images */
    public $OXTHUMB;
    public $OXICON;
    public $OXPIC1;
    public $OXPIC2;
    public $OXPIC3;
    public $OXPIC4;
    public $OXPIC5;
    public $OXPIC6;
    public $OXPIC7;
//    public $OXPIC8;
//    public $OXPIC9;
//    public $OXPIC10;
//    public $OXPIC11;
//    public $OXPIC12;

    /* dimensions */
    public $OXWEIGHT;
    public $OXLENGTH;
    public $OXWIDTH;
    public $OXHEIGHT;

    /* stock and delivery */
    public $OXSTOCK;
    public $OXVARSTOCK;
    public $OXSTOCKFLAG;
    public $OXDELIVERY;
    public $OXMINDELTIME;
    public $OXMAXDELTIME;
    public $OXDELTIMEUNIT;
//    public $OXSTOCKTEXT;
//    public $OXNOSTOCKTEXT;

    /* rating */
    public $OXRATING;
    public $OXRATINGCNT;

    /* timestamps */
    public $OXINSERT;
    public $OXTIMESTAMP;

    /* digital product */
    public $OXFILE;
    public $OXISDOWNLOADABLE;
    public $OXNONMATERIAL;

    public $OXISCONFIGURABLE;
    public $OXFREESHIPPING;
    public $OXSOLDAMOUNT;

    /* Backport from Oxid 6.0 */
    public $OXHIDDEN;

//    /* external url */
//    public $OXEXTURL;
//    public $OXURLDESC;
//    public $OXURLIMG;

//    /* OXID internal */
//    public $OXREMINDACTIVE;
//    public $OXREMINDAMOUNT;
//    public $OXAMITEMID;
//    public $OXAMTASKID;
//    public $OXFOLDER;
//    public $OXTEMPLATE;
//    public $OXSUBCLASS;
//    public $OXQUESTIONEMAIL;
//    public $OXSHOWCUSTOMAGREEMENT;
//    public $OXSKIPDISCOUNTS;
//    public $OXBUNDLEID;
}
