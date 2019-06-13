<?php

namespace Makaira\Connect\Type\Category;

use Makaira\Connect\Type;

/**
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class Category extends Type
{
    public $oxobject = [];

    public $OXACTIVE;
    public $OXHIDDEN;
    public $OXPARENTID;
    public $OXLEFT;
    public $OXRIGHT;
    public $OXROOTID;
    public $OXSORT;
    public $OXSHOPID;
    public $OXTITLE;
    public $OXDESC;
    public $OXLONGDESC;
    public $OXTHUMB;
    public $OXEXTLINK;
    public $OXTEMPLATE;
    public $OXDEFSORT;
    public $OXDEFSORTMODE;
    public $OXPRICEFROM;
    public $OXPRICETO;
    public $OXICON;
    public $OXPROMOICON;
    public $OXVAT;
    public $OXSKIPDISCOUNTS;
    public $OXSHOWSUFFIX;
    public $OXTIMESTAMP;

    public $MARM_OXSEARCH_FILTERS;
    public $MARM_OXSEARCH_MAPPINGS;
    public $MARMOXSEARCHSEARCHKEYS;
}
