<?php declare(strict_types = 1);

namespace App\Foundation\Database;

use App\Foundation\Config\Config;
use PDO;

class MySqlConnection implements DBConnection
{
    private PDO $pdo;

    public function __construct(Config $config)
    {
        $this->pdo = new PDO(
            $this->getDsn($config),
            $config->get('DB_USERNAME'),
            $config->get('DB_PASSWORD'),
            [
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
            ]
        );
    }

    private function getDsn(Config $config): string
    {
        $host = $config->get('DB_HOST');
        $port = $config->get('DB_PORT');
        $database = $config->get('DB_DATABASE');
        return 'mysql:host=' . $host . ':' . $port . ';dbname=' . $database;
    }

    public function insertGetId(string $sql, ?array $params = null): int
    {
        $this->insert($sql, $params);
        return (int)$this->pdo->lastInsertId();
    }

    public function insert(string $sql, ?array $params = null)
    {
        $stm = $this->pdo->prepare($sql);
        $stm->execute($params);
    }

    public function get()
    {
        // TODO: Implement get() method.
    }
}
