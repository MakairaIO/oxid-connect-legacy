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
    public $shortdesc;
    public $longdesc;
    public $meta_keywords;
    public $meta_description;
    public $selfLinks = [];
    public $hierarchy;
    public $depth;
    public $subcategories = [];
    public $hidden = false;
}
