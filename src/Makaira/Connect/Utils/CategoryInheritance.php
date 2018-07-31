<?php
/**
 * This file is part of a marmalade GmbH project
 * It is not Open Source and may not be redistributed.
 * For contact information please visit http://www.marmalade.de
 * Version:    1.0
 * Author:     Jens Richter <richter@marmalade.de>
 * Author URI: http://www.marmalade.de
 */

namespace Makaira\Connect\Utils;

use Makaira\Connect\DatabaseInterface;
use Makaira\Query;

class CategoryInheritance
{
    private $database;
    private $useCategoryInheritance;
    private $categoryAggregationId;

    /**
     * CategoryInheritance constructor.
     *
     * @param DatabaseInterface $database
     * @param bool              $useCategoryInheritance
     * @param bool              $categoryAggregationId
     *
     */
    public function __construct(DatabaseInterface $database, $useCategoryInheritance, $categoryAggregationId)
    {
        $this->database               = $database;
        $this->useCategoryInheritance = $useCategoryInheritance;
        $this->categoryAggregationId  = $categoryAggregationId;
    }

    /**
     * @param Query $query
     *
     * @SuppressWarnings(CyclomaticComplexity)
     */
    public function applyToAggregation(Query $query)
    {
        if (!$this->useCategoryInheritance ||
            !$this->categoryAggregationId ||
            !isset($query->aggregations[$this->categoryAggregationId])) {
            return;
        }

        $key = $this->categoryAggregationId;

        $childCategories = [];
        foreach ($query->aggregations[$key] as $selectedId) {
            $oCategory = oxNew('oxcategory');
            $oCategory->load($selectedId);
            if ($oCategory) {
                $childCategories[$selectedId] = $this->database->getColumn(
                    "SELECT OXID FROM oxcategories WHERE OXROOTID = :rootId AND OXLEFT > :left AND OXRIGHT < :right",
                    [
                        'rootId' => $oCategory->oxcategories__oxrootid->value,
                        'left' =>$oCategory->oxcategories__oxleft->value,
                        'right'=>$oCategory->oxcategories__oxright->value,
                    ]
                );
            }
        }
        // FIXME: do we really need this loop
        foreach ($childCategories as $parentId => $childIds) {
            if ($intersection = array_intersect($query->aggregations[$key], (array) $childIds)) {
                foreach ($intersection as $childId) {
                    unset($childCategories[$childId]);
                }
            }
        }
        $categoryIds               = array_unique(array_keys($childCategories));
        $query->aggregations[$key] = $categoryIds;

        foreach ($childCategories as $parentId => $childIds) {
            $categoryIds = array_merge($categoryIds, (array) $childIds);
        }
        $categoryIds               = array_unique($categoryIds);
        $query->aggregations[$key] = $categoryIds;
    }

    /**
     * @param $categoryId
     *
     * @return array
     */
    public function buildCategoryInheritance($categoryId)
    {
        if (!isset($categoryId) || !$this->useCategoryInheritance) {
            return $categoryId;
        }

        $oCategory = oxNew('oxcategory');
        $oCategory->load($categoryId);
        if ($oCategory) {
            $result     = $this->database->getColumn(
                "SELECT OXID FROM oxcategories WHERE OXROOTID = :rootId AND OXLEFT > :left AND OXRIGHT < :right",
                [
                    'rootId' => $oCategory->oxcategories__oxrootid->value,
                    'left' => $oCategory->oxcategories__oxleft->value,
                    'right' =>$oCategory->oxcategories__oxright->value
                ]
            );
            $categoryId = array_merge(
                (array) $categoryId,
                $result
            );
        }

        return $categoryId;
    }
}
