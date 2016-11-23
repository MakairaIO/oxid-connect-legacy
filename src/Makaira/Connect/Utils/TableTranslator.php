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
        $replaceTables = [];
        $searchTables  = [];
        $callback      = function ($match) use (&$replaceTables) {
            $tableName = $match['tableName'];

            return $replaceTables[$tableName] . $match['end'];
        };
        foreach ($this->searchTables as $searchTable) {
            $replaceTables[$searchTable]                                             =
                "oxv_{$searchTable}_{$this->language}";
            $searchTables["((?P<tableName>{$searchTable})(?P<end>[^A-Za-z0-9_]|$))"] = $callback;
        }
        $sql = preg_replace_callback_array($searchTables, $sql);

        return $sql;
    }

}
