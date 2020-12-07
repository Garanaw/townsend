<?php declare(strict_types = 1);

namespace App\Foundation\Database;

use App\Domain\Contact\User;
use App\Foundation\Config\Config;
use Illuminate\Support\Collection;
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

    public function insertGetId(string $table, array $fields): int
    {
        $this->insert($table, $fields);
        return (int)$this->pdo->lastInsertId();
    }

    public function insert(string $table, array $fields)
    {
        $columns = implode(', ', array_keys($fields));
        $values = collect($fields)
            ->keys()
            ->map(fn(string $field): string => ":$field")
            ->implode(', ');
        $sql = "INSERT INTO $table ($columns) values ($values);";
        $stm = $this->pdo->prepare($sql);
        $stm->execute($fields);
    }

    public function update(string $table, array $values, array $where)
    {
        $set = collect($values)
            ->map(static fn(mixed $value, string $column): string => "$column=:$column")
            ->implode(', ');
        $compiledWhere = $this->compileWhere($where);
        $sql = "UPDATE TABLE $table SET $set $compiledWhere";
        return $this->pdo->prepare($sql)->execute(array_merge($values, $where));
    }

    public function get(string $table, array $where): Collection
    {
        $compiledWhere = $this->compileWhere($where);
        $stm = $this->pdo->prepare("SELECT * FROM $table WHERE $compiledWhere");
        $stm->execute($where);
        return collect($stm->fetchAll(PDO::FETCH_ASSOC));
    }

    protected function compileWhere(array $where): string
    {
        return collect($where)
            ->map(static fn (mixed $value, string $column): string => "$column=:$column")
            ->implode(' AND ');
    }
}
