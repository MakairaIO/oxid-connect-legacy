<?php

class oxLegacyDb {
}

class oxDb {
    public static function getInstance()
    {
        return new static();
    }

    public function getDb()
    {
        return new oxLegacyDb();
    }
}
