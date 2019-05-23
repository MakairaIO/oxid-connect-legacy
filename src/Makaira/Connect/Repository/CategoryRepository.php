<?php

namespace Makaira\Connect\Repository;

use Makaira\Connect\Type\Category\Category;

class CategoryRepository extends AbstractRepository
{
    /**
     * Get TYPE of repository.
     *
     * @return string
     */
    public function getType()
    {
        return 'category';
    }

    /**
     * Get an instance of current type.
     *
     * @return Category
     */
    public function getInstance($id)
    {
        return new Category($id);
    }

    protected function getSelectQuery()
    {
        return "
          SELECT
            oxcategories.OXID as `id`,
            UNIX_TIMESTAMP(oxcategories.oxtimestamp) AS `timestamp`,
            oxcategories.*
          FROM
            oxcategories
          WHERE
            oxcategories.oxid = :id
        ";
    }

    protected function getAllIdsQuery()
    {
        return "
          SELECT
           OXID
          FROM
           oxcategories;
        ";
    }

    protected function getParentIdQuery()
    {
        return "
          SELECT
            OXPARENTID
          FROM
            oxcategories
          WHERE
            oxcategories.oxid = :id
        ";
    }
}
