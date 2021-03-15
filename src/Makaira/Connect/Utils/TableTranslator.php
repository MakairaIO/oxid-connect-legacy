<?php

namespace Makaira\Connect\Utils;

use Closure;

class TableTranslator
{
    private $searchTables = [];

    private $language = 'de';

    /**
     * @var Closure
     */
    private $viewNameGenerator;

    /**
     * TableTranslator constructor.
     *
     * @param string[] $searchTables
     */
    public function __construct(array $searchTables)
    {
        $this->searchTables = $searchTables;

        $this->viewNameGenerator = static function ($table, $language) {
            return "oxv_{$table}_{$language}";
        };
    }

    /**
     * @param Closure $viewNameGenerator
     *
     * @return TableTranslator
     */
    public function setViewNameGenerator(Closure $viewNameGenerator): TableTranslator
    {
        $this->viewNameGenerator = $viewNameGenerator;

        return $this;
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
            $replaceTable = ($this->viewNameGenerator)($searchTable, $this->language);
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
