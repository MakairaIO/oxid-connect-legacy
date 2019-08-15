<?php

namespace Makaira\Connect\Type\Category;

use Makaira\Connect\Type;

/**
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class Category extends Type
{
    public $category_title;
    public $sort;
    public $mak_shortdesc;
    public $longdesc;
    public $mak_meta_keywords;
    public $mak_meta_description;
    public $selfLinks = [];
    public $hierarchy;
    public $depth;
    public $subcategories = [];

    //    public $oxobject = [];
    //    public $OXACTIVE;
    //    public $OXHIDDEN;
    //    public $OXPARENTID;
    //    public $OXLEFT;
    //    public $OXRIGHT;
    //    public $OXROOTID;
    //    public $OXSORT;
    //    public $OXSHOPID;
    //    public $OXTITLE;
    //    public $OXDESC;
    //    public $OXLONGDESC;
    //    public $OXTHUMB;
    //    public $OXEXTLINK;
    //    public $OXTEMPLATE;
    //    public $OXDEFSORT;
    //    public $OXDEFSORTMODE;
    //    public $OXPRICEFROM;
    //    public $OXPRICETO;
    //    public $OXICON;
    //    public $OXPROMOICON;
    //    public $OXVAT;
    //    public $OXSKIPDISCOUNTS;
    //    public $OXSHOWSUFFIX;
    //    public $OXTIMESTAMP;
}
