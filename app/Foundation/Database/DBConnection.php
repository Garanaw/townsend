<?php declare(strict_types = 1);

namespace App\Foundation\Database;

use Illuminate\Support\Collection;

interface DBConnection
{
    public function insert(string $table, array $fields);

    public function update(string $table, array $values, array $where);

    public function get(string $table, array $where): Collection;
}
