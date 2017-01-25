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

use Makaira\Connect\Change;
use Makaira\Connect\DatabaseInterface;
use Makaira\Connect\Type\Manufacturer\Manufacturer;

class ManufacturerRepository
{
    protected $selectQuery = "
      SELECT
        oxmanufacturers.OXID as `id`,
        UNIX_TIMESTAMP(oxmanufacturers.oxtimestamp) AS `timestamp`,
        oxmanufacturers.*
      FROM
        oxmanufacturers
      WHERE
        oxmanufacturers.oxid = :id
    ";
    protected $allIdsQuery = "
      SELECT
       OXID
      FROM
       oxmanufacturers;
    ";
    /**
     * @var DatabaseInterface
     */
    private $database;
    /**
     * @var ModifierList
     */
    private $modifiers;

    public function __construct(DatabaseInterface $database, ModifierList $modifiers)
    {
        $this->database  = $database;
        $this->modifiers = $modifiers;
    }

    public function get($id)
    {
        $result = $this->database->query($this->selectQuery, ['id' => $id]);

        $change = new Change();

        if (empty($result)) {
            $change->deleted = true;

            return $change;
        }
        $manufacturer     = new Manufacturer($result[0]);
        $manufacturer     = $this->modifiers->applyModifiers($manufacturer, $this->database);
        $change->data = $manufacturer;

        return $change;
    }

    /**
     * Get TYPE of repository.
     *
     * @return string
     * @codeCoverageIgnore
     */
    public function getType()
    {
        return 'manufacturer';
    }

    /**
     * Get all IDs handled by this repository.
     *
     * @return string[]
     */
    public function getAllIds()
    {
        $result = $this->database->query($this->allIdsQuery);

        return array_map(
            function ($row) {
                return $row['OXID'];
            },
            $result
        );
    }
}
