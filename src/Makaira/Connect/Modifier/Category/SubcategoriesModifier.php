<?php

namespace Makaira\Connect\Modifier\Category;

use Makaira\Connect\Modifier;
use Makaira\Connect\Type;
use Makaira\Connect\Utils\CategoryInheritance;

class SubcategoriesModifier extends Modifier
{
    /**
     * @var DatabaseInterface
     */
    private $categoryInheritance;

    public function __construct(CategoryInheritance $categoryInheritance)
    {
        $this->categoryInheritance = $categoryInheritance;
    }
    public function apply(Type $category)
    {
        $category->subcategories = $this->categoryInheritance->buildCategoryInheritance($category->id);

        return $category;
    }
}
