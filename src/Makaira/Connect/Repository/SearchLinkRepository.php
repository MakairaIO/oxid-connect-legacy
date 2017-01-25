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
use Makaira\Connect\Type\SearchLink\SearchLink;

class SearchLinkRepository
{
    protected $selectQuery = "
      SELECT
        marm_oxsearch_searchlinks.OXID as `id`,
        marm_oxsearch_searchlinks.*
      FROM
        marm_oxsearch_searchlinks
      WHERE
        marm_oxsearch_searchlinks.oxid = :id
    ";
    protected $allIdsQuery = "
      SELECT
       OXID
      FROM
       marm_oxsearch_searchlinks;
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
        $searchLink = new SearchLink($result[0]);
        $searchLink = $this->modifiers->applyModifiers($searchLink, $this->database);
        $change->data = $searchLink;

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
        return 'searchlink';
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
