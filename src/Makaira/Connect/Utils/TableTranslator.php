<?php

namespace Makaira\Connect\Utils;

class TableTranslator
{
    private $searchTables = [];

    private $language = 'de';

    /**
     * TableTranslator constructor.
     *
     * @param string[] $searchTables
     */
    public function __construct(array $searchTables)
    {
        $this->searchTables = $searchTables;
    }

    /**
     * Set the language
     *
     * @param string $language
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * Translate an sql query
     *
     * @param string $sql
     *
     * @return string
     */
    public function translate($sql)
    {
        foreach ($this->searchTables as $searchTable) {
            $replaceTable = "oxv_{$searchTable}_{$this->language}";
            $sql          = preg_replace_callback(
                "((?P<tableName>{$searchTable})(?P<end>[^A-Za-z0-9_]|$))",
                function ($match) use ($replaceTable) {
                    return $replaceTable . $match['end'];
                },
                $sql
            );
        }

        return $sql;
    }
}
