<?php

namespace Makaira\Connect\Repository;

use Makaira\Connect\Type\Product\Product;

class ProductRepository extends AbstractRepository
{
    /**
     * Get TYPE of repository.
     *
     * @return string
     */
    public function getType()
    {
        return 'product';
    }

    /**
     * Get an instance of current type.
     *
     * @return Product
     */
    protected function getInstance($id)
    {
        return new Product($id);
    }

    protected function getSelectQuery()
    {
        return "
            SELECT
                oxarticles.OXID as `id`,
                UNIX_TIMESTAMP(oxarticles.oxtimestamp) AS `timestamp`,
                oxarticles.*,
                oxartextends.oxlongdesc AS `OXLONGDESC`,
                oxartextends.oxtags AS `OXTAGS`,
                oxmanufacturers.oxtitle AS manufacturer_title
            FROM
                oxarticles
                LEFT JOIN oxartextends ON oxarticles.oxid = oxartextends.oxid
                LEFT JOIN oxmanufacturers ON oxarticles.oxmanufacturerid = oxmanufacturers.oxid
            WHERE
                oxarticles.oxid = :id
                AND oxarticles.oxparentid = ''
        ";
    }

    protected function getAllIdsQuery()
    {
        return "
          SELECT
            OXID
          FROM
            oxarticles
          WHERE
            OXPARENTID = ''
        ";
    }

    protected function getParentIdQuery()
    {
        return "
          SELECT
            OXPARENTID
          FROM
            oxarticles
          WHERE
            oxarticles.oxid = :id
        ";
    }

    public function getParentId($id)
    {
        $result = $this->database->getColumn($this->getParentIdQuery(), ['id' => $id]);

        if ($result && isset($result[0])) {
            return $result[0];
        }

        return null;
    }
}
