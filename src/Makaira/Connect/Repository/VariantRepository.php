<?php

namespace Makaira\Connect\Repository;

use Makaira\Connect\Change;
use Makaira\Connect\DatabaseInterface;
use Makaira\Connect\RepositoryInterface;
use Makaira\Connect\Type\Variant\Variant;

class VariantRepository implements RepositoryInterface
{
    protected $selectQuery = "
        SELECT
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
        $this->database = $database;
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
        $variant = new Variant($result[0]);
        $variant = $this->modifiers->applyModifiers($variant);
        $change->data = $variant;
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
        return 'variant';
    }
}
