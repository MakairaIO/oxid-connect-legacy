<?php

namespace Makaira\Connect\Repository;

use Makaira\Connect\Repository\ProductRepository;
use Makaira\Connect\Type\Variant\Variant;

class VariantRepository extends ProductRepository
{
    /**
     * Get TYPE of repository.
     *
     * @return string
     */
    public function getType()
    {
        return 'variant';
    }

    /**
     * Get an instance of current type.
     *
     * @return Variant
     */
    protected function getInstance($id)
    {
        return new Variant($id);
    }

    protected function getSelectQuery()
    {
        return "
            SELECT
                oxarticles.OXID as `id`,
                oxarticles.oxparentid AS `parent`,
                UNIX_TIMESTAMP(oxarticles.oxtimestamp) AS `timestamp`,
                oxarticles.*,
                oxartextends.oxlongdesc AS `OXLONGDESC`,
                oxartextends.oxtags AS `OXTAGS`
            FROM
                oxarticles
                LEFT JOIN oxartextends ON oxarticles.oxid = oxartextends.oxid
            WHERE
                oxarticles.oxid = :id
                AND oxarticles.oxparentid != ''
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
            OXPARENTID != ''
        ";
    }
}
