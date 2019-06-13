<?php

namespace Makaira\Connect;

use Makaira\Import\Changes;

class Repository
{
    protected $cleanupQuery = "
        DELETE FROM
          makaira_connect_changes
        WHERE
          changed < DATE_SUB(NOW(), INTERVAL 1 DAY);
    ";

    /**
     * @var DatabaseInterface
     */
    private $database;

    protected $selectQuery = "
        SELECT
            makaira_connect_changes.sequence,
            makaira_connect_changes.oxid AS `id`,
            makaira_connect_changes.type
        FROM
            makaira_connect_changes
        WHERE
            makaira_connect_changes.sequence > :since
        ORDER BY
            sequence ASC
        LIMIT :limit
    ";

    protected $touchQuery = "
        REPLACE INTO
          makaira_connect_changes
        (OXID, TYPE, CHANGED)
          VALUES
        (:id, :type, NOW());
    ";

    /**/
    private $parentProducts = [];

    /**/
    private $propsExclude = [
        'attribute',
        'attributeStr',
        'attributeInt',
        'attributeFloat',
    ];

    /**/
    private $propsNullValues = [null, '', []];

    /**/
    private $propsSpecial = [];

    /**
     * Repository constructor.
     *
     * @param \Makaira\Connect\DatabaseInterface $database
     * @param array                              $repositoryMapping
     */
    public function __construct(DatabaseInterface $database, array $repositoryMapping = array())
    {
        $this->database          = $database;
        $this->repositoryMapping = $repositoryMapping;
    }

    /**
     * Fetch and serialize changes.
     *
     * @param int $since Sequence offset
     * @param int $limit Fetch limit
     *
     * @return Changes
     */
    public function getChangesSince($since, $limit = 50)
    {
        $result = $this->database->query($this->selectQuery, ['since' => $since ?: 0, 'limit' => $limit]);

        $changes = array();
        foreach ($result as $row) {
            try {
                $productRepository = $this->getRepositoryForType('product');
                $typeProduct       = $productRepository->getType();
                $variantRepository = $this->getRepositoryForType('variant');
                $typeVariant       = $variantRepository->getType();

                $type     = $row['type'];
                $sequence = $row['sequence'];
                $id       = $row['id'];
                $parentId = null;

                if ($typeVariant === $type) {
                    $parentId = $productRepository->getParentId($id);

                    if ($parentId && !isset($this->parentProducts[ $parentId ])) {
                        $change                            = $productRepository->get($parentId);
                        $this->parentProducts[ $parentId ] = $change;
                        unset($change);
                    }
                }

                $change           = $this->getRepositoryForType($type)->get($id);
                $change->sequence = $sequence;

                if ($typeVariant === $type && $parentId) {
                    foreach ($change->data as $_key => $_data) {
                        if (in_array($_key, $this->propsExclude, false)) {
                            continue;
                        }
                        $nullValues =
                            isset($this->propsSpecial[ $_key ]) ? $this->propsSpecial[ $_key ] : $this->propsNullValues;
                        if (in_array($_data, $nullValues, true)) {
                            $change->data->$_key = $this->parentProducts[ $parentId ]->data->$_key;
                        }
                    }
                }

                $changes[] = $change;

                if ($typeProduct === $type &&
                    (true === $change->deleted ||
                        (isset($change->data->OXVARCOUNT) && 0 === $change->data->OXVARCOUNT))) {
                    $pChange                   = clone $change;
                    $pChange->data->parent     = $id;
                    $pChange->data->OXPARENTID = $id;
                    $pChange->id               = md5($id . '.variant.new');
                    $pChange->sequence         = $sequence;
                    $pChange->type             = $typeVariant;

                    $changes[] = $pChange;
                    unset($pChange);
                }

                unset($change);
            } catch (\OutOfBoundsException $e) {
                // catch no repository found exception
            }
        }

        return new Changes(
            array(
                'since'          => $since,
                'count'          => count($changes),
                'requestedCount' => $limit,
                'changes'        => $changes,
            )
        );
    }

    public function countChangesSince($since)
    {
        $result = $this->database->query(
            'SELECT
                COUNT(*) count
            FROM
                makaira_connect_changes
            WHERE
                makaira_connect_changes.sequence > :since',
            ['since' => $since ?: 0]
        );

        return $result[0]['count'];
    }

    protected function getRepositoryForType($type)
    {
        if (!isset($this->repositoryMapping[ $type ])) {
            throw new \OutOfBoundsException("No repository defined for type " . $type);
        }

        return $this->repositoryMapping[ $type ];
    }

    /**
     * Mark an object as updated.
     *
     * @param string $type
     * @param string $id
     */
    public function touch($type, $id)
    {
        if (!$id) {
            return;
        }
        $this->database->execute($this->touchQuery, ['type' => $type, 'id' => $id]);
    }

    /**
     * Clean up changes list.
     *
     * @ignoreCodeCoverage
     */
    public function cleanup()
    {
        $this->database->execute($this->cleanupQuery);
    }

    /**
     * Add all items to the changes list.
     */
    public function touchAll()
    {
        $this->cleanUp();

        /**
         * @var string              $type
         * @var RepositoryInterface $repository
         */
        foreach ($this->repositoryMapping as $type => $repository) {
            foreach ($repository->getAllIds() as $id) {
                $this->touch($type, $id);
            }
        }
    }
}
