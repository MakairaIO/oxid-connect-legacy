<?php

namespace Makaira\Connect\Repository;

class Product
{
    private $database;

    public function __construct(\oxDB $database)
    {
        $this->database = $database;
    }

    public function getChangesSince($since)
    {
        return array();
    }
}
