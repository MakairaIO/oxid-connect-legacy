<?php

namespace Makaira\Connect\Type\Common;

/**
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class BaseProduct extends \Kore\DataObject\DataObject
{
    public $id;
    public $timestamp;
    public $OXID;
    public $OXSHOPID;
    public $OXPARENTID;
    public $OXACTIVE;
    public $OXACTIVEFROM;
    public $OXACTIVETO;
    public $OXARTNUM;
    public $OXEAN;
    public $OXDISTEAN;
    public $OXMPN;
    public $OXTITLE;
    public $OXSHORTDESC;
    public $OXPRICE;
    public $OXBLFIXEDPRICE;
    public $OXPRICEA;
    public $OXPRICEB;
    public $OXPRICEC;
    public $OXBPRICE;
    public $OXTPRICE;
    public $OXUNITNAME;
    public $OXUNITQUANTITY;
    public $OXEXTURL;
    public $OXURLDESC;
    public $OXURLIMG;
    public $OXVAT;
    public $OXTHUMB;
    public $OXICON;
    public $OXPIC1;
    public $OXPIC2;
    public $OXPIC3;
    public $OXPIC4;
    public $OXPIC5;
    public $OXPIC6;
    public $OXPIC7;
    public $OXPIC8;
    public $OXPIC9;
    public $OXPIC10;
    public $OXPIC11;
    public $OXPIC12;
    public $OXWEIGHT;
    public $OXSTOCK;
    public $OXSTOCKFLAG;
    public $OXSTOCKTEXT;
    public $OXNOSTOCKTEXT;
    public $OXDELIVERY;
    public $OXINSERT;
    public $OXTIMESTAMP;
    public $OXLENGTH;
    public $OXWIDTH;
    public $OXHEIGHT;
    public $OXFILE;
    public $OXSEARCHKEYS;
    public $OXTEMPLATE;
    public $OXQUESTIONEMAIL;
    public $OXISSEARCH;
    public $OXISCONFIGURABLE;
    public $OXVARNAME;
    public $OXVARSTOCK;
    public $OXVARCOUNT;
    public $OXVARSELECT;
    public $OXVARMINPRICE;
    public $OXVARMAXPRICE;
    public $OXVARNAME_1;
    public $OXVARSELECT_1;
    public $OXVARNAME_2;
    public $OXVARSELECT_2;
    public $OXVARNAME_3;
    public $OXVARSELECT_3;
    public $OXTITLE_1;
    public $OXSHORTDESC_1;
    public $OXURLDESC_1;
    public $OXSEARCHKEYS_1;
    public $OXTITLE_2;
    public $OXSHORTDESC_2;
    public $OXURLDESC_2;
    public $OXSEARCHKEYS_2;
    public $OXTITLE_3;
    public $OXSHORTDESC_3;
    public $OXURLDESC_3;
    public $OXSEARCHKEYS_3;
    public $OXBUNDLEID;
    public $OXFOLDER;
    public $OXSUBCLASS;
    public $OXSTOCKTEXT_1;
    public $OXSTOCKTEXT_2;
    public $OXSTOCKTEXT_3;
    public $OXNOSTOCKTEXT_1;
    public $OXNOSTOCKTEXT_2;
    public $OXNOSTOCKTEXT_3;
    public $OXSORT;
    public $OXSOLDAMOUNT;
    public $OXNONMATERIAL;
    public $OXFREESHIPPING;
    public $OXREMINDACTIVE;
    public $OXREMINDAMOUNT;
    public $OXAMITEMID;
    public $OXAMTASKID;
    public $OXVENDORID;
    public $OXMANUFACTURERID;
    public $OXSKIPDISCOUNTS;
    public $OXRATING;
    public $OXRATINGCNT;
    public $OXMINDELTIME;
    public $OXMAXDELTIME;
    public $OXDELTIMEUNIT;
    public $OXUPDATEPRICE;
    public $OXUPDATEPRICEA;
    public $OXUPDATEPRICEB;
    public $OXUPDATEPRICEC;
    public $OXUPDATEPRICETIME;
    public $OXISDOWNLOADABLE;
    public $OXSHOWCUSTOMAGREEMENT;

    public $OXHIDDEN;

    public $OXLONGDESC;
    public $OXTAGS;

    public $MARM_OXSEARCH_BOOST;
    public $MARM_OXSEARCH_PROFITMARGIN;
    public $active    = true;
    public $attribute = [];
}
