<?php declare(strict_types = 1);

namespace App\Foundation\Database;

interface DBConnection
{
    public function insert(string $sql, ?array $params = null);

    public function get();
}
