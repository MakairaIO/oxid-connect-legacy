<?php
/**
 * This file is part of a marmalade GmbH project
 * It is not Open Source and may not be redistributed.
 * For contact information please visit http://www.marmalade.de
 * Version:    1.0
 * Author:     Jens Richter <richter@marmalade.de>
 * Author URI: http://www.marmalade.de
 */

namespace Makaira\Connect\Repository;

use Makaira\Connect\Type\Manufacturer\Manufacturer;

class ManufacturerRepository extends AbstractRepository
{
    /**
     * Get TYPE of repository.
     *
     * @return string
     */
    public function getType()
    {
        return 'manufacturer';
    }

    /**
     * Get an instance of current type.
     *
     * @return Manufacturer
     */
    protected function getInstance($id)
    {
        return new Manufacturer($id);
    }

    protected function getSelectQuery()
    {
        return "
          SELECT
            oxmanufacturers.OXID as `id`,
            UNIX_TIMESTAMP(oxmanufacturers.oxtimestamp) AS `timestamp`,
            oxmanufacturers.*
          FROM
            oxmanufacturers
          WHERE
            oxmanufacturers.oxid = :id
        ";
    }

    protected function getAllIdsQuery()
    {
        return "
          SELECT
           OXID
          FROM
           oxmanufacturers;
        ";
    }
}
