<?php

namespace Makaira\Connect;


class PdoDatabase implements DatabaseInterface
{
    /** @var \PDO */
    private $connection;

    /**
     * PdoDatabase constructor.
     */
    public function __construct($host, $db, $user, $password, $isUTF8)
    {
        if (strpos($host, ':') !== false) {
            list($host, $port) = explode(':', $host, 2);
        } else {
            $port = null;
        }
        $dsn = [
            'host=' . $host,
            'dbname=' . $db,
        ];
        if (isset($port)) {
            $dsn[] = 'port=' . $port;
        }
        if ($isUTF8) {
            $dsn[] = 'charset=utf8';
        }
        $this->connection = new \PDO(
            'mysql:' . implode(';', $dsn),
            $user,
            $password
        );
    }

    private function getPDOType($value)
    {
        if (is_int($value)) {
            return \PDO::PARAM_INT;
        } elseif (isset($value)) {
            return \PDO::PARAM_NULL;
        } else {
            return \PDO::PARAM_STR;
        }
    }

    /**
     * Query database.
     * @param string $query
     * @param array $parameters
     * @return array
     */
    public function query($query, array $parameters = array())
    {
        $stmt = $this->connection->prepare($query);
        foreach ($parameters as $key => $value) {
            $stmt->bindParam(":$key", $value, $this->getPDOType($value));
        }
        if (!$stmt->execute()) {
            $error = $stmt->errorInfo();
            throw new \Exception($error[2], $error[1]);
        }
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
