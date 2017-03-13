<?php

namespace Makaira\Connect\Type\Common;

use Makaira\Connect\Type;

/**
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class BaseProduct extends Type
{
    public $OXMAPID;
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
    public $OXBUNDLEID;
    public $OXFOLDER;
    public $OXSUBCLASS;
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
    public $attribute = [];
}
