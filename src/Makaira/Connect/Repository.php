<?php

namespace Makaira\Connect;

use Makaira\Import\Changes;

class Repository
{
    /**
     * @var string
     */
    protected $cleanupQuery = '
        DELETE FROM
          makaira_connect_changes
        WHERE
          changed < DATE_SUB(NOW(), INTERVAL 1 DAY);
    ';

    /**
     * @var DatabaseInterface
     */
    private $database;

    /**
     * @var string
     */
    protected $selectQuery = '
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
    ';

    /**
     * @var string
     */
    protected $touchQuery = '
        REPLACE INTO
          makaira_connect_changes
        (OXID, TYPE, CHANGED)
          VALUES
        (:id, :type, NOW());
    ';

    /**
     * @var array
     */
    private $parentProducts = [];

    /**
     * @var array
     */
    private $parentAttributes = [];

    /**
     * @var array
     */
    private $propsExclude = [
        'attributes',
        'attributeStr',
        'attributeInt',
        'attributeFloat',
        '_attributeStr',
        '_attributeInt',
        '_attributeFloat',
    ];

    /**
     * @var array
     */
    private $propsInclude = [
        'OXISSEARCH',
    ];

    /**
     * @var array
     */
    private $propsDoNotClone = [
        'attributes',
        'tmpAttributeStr',
        'tmpAttributeInt',
        'tmpAttributeFloat',
    ];

    /**
     * @var array
     */
    private $propsNullValues = [null, '', []];

    /**
     * @var array
     */
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
     * @SuppressWarnings(CyclomaticComplexity)
     * @SuppressWarnings(NPathComplexity)
     */
    public function getChangesSince($since, $limit = 50)
    {
        $result = $this->database->query($this->selectQuery, ['since' => $since ?: 0, 'limit' => $limit]);

        $changes           = array();
        $productRepository = $this->getRepositoryForType('product');
        $typeProduct       = $productRepository->getType();
        $variantRepository = $this->getRepositoryForType('variant');
        $typeVariant       = $variantRepository->getType();
        foreach ($result as $row) {
            try {
                $type     = $row['type'];
                $sequence = $row['sequence'];
                $id       = $row['id'];
                $parentId = null;

                if ($typeVariant === $type) {
                    $parentId = $productRepository->getParentId($id);

                    if ($parentId && !isset($this->parentProducts[ $parentId ])) {
                        $change = $productRepository->get($parentId);
                        $this->setParentCache($parentId, $change);
                        unset($change);
                    }
                }

                $change           = $this->getRepositoryForType($type)->get($id);
                $change->sequence = $sequence;

                if ($typeVariant === $type && $parentId) {
                    unset(
                        $change->data->tmpAttributeStr,
                        $change->data->tmpAttributeInt,
                        $change->data->tmpAttributeFloat
                    );

                    foreach ($change->data as $_key => $_data) {
                        if (in_array($_key, $this->propsExclude, false)) {
                            continue;
                        }
                        $nullValues =
                            isset($this->propsSpecial[ $_key ]) ? $this->propsSpecial[ $_key ] : $this->propsNullValues;
                        if (in_array($_key, $this->propsInclude, false) || in_array($_data, $nullValues, true)) {
                            $change->data->$_key = $this->parentProducts[ $parentId ]->data->$_key;
                        }
                    }
                    $change->data->attributeStr   = array_merge(
                        (array) $this->parentAttributes[ $parentId ]['attributeStr'],
                        $change->data->attributeStr
                    );
                    $change->data->attributeInt   = array_merge(
                        (array) $this->parentAttributes[ $parentId ]['attributeInt'],
                        $change->data->attributeInt
                    );
                    $change->data->attributeFloat = array_merge(
                        (array) $this->parentAttributes[ $parentId ]['attributeFloat'],
                        $change->data->attributeFloat
                    );
                }

                if ($typeProduct === $type) {
                    if (true === $change->deleted ||
                        (isset($change->data->OXVARCOUNT) && 0 === $change->data->OXVARCOUNT)) {
                        $pChange                  = clone $change;
                        $pChange->data->isVariant = true;
                        foreach ($this->propsDoNotClone as $_props) {
                            if (isset($pChange->data->$_props)) {
                                unset($pChange->data->$_props);
                            }
                        }
                        $pChange->data->parent = $id;
                        if (isset($pChange->data->OXPARENTID)) {
                            $pChange->data->OXPARENTID = $id;
                        }
                        $pChange->id       = md5($id . '.variant.new');
                        $pChange->data->id = $pChange->id;
                        if (isset($pChange->data->OXID)) {
                            $pChange->data->OXID = $pChange->id;
                        }
                        $pChange->sequence = $sequence;
                        $pChange->type     = $typeVariant;

                        $changes[] = $pChange;
                        unset($pChange);
                    } else {
                        $this->setParentCache($id, $change);
                    }
                }

                $changes[] = $change;
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

    protected function setParentCache($parentId, &$parentData)
    {
        $this->parentAttributes[ $parentId ] = [
            'attributeStr'   => $parentData->data->tmpAttributeStr,
            'attributeInt'   => $parentData->data->tmpAttributeInt,
            'attributeFloat' => $parentData->data->tmpAttributeFloat,
        ];
        unset(
            $parentData->data->tmpAttributeStr,
            $parentData->data->tmpAttributeInt,
            $parentData->data->tmpAttributeFloat
        );

        $this->parentProducts[ $parentId ] = $parentData;
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
