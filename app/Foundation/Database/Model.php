<?php declare(strict_types = 1);

namespace App\Foundation\Database;

use App\Foundation\Container;
use Illuminate\Support\Str;

abstract class Model
{
    protected DBConnection $connection;
    protected array $original;
    protected array $fillable = [];
    protected ?int $id = null;

    public function __construct(?array $fillable = [])
    {
        $this->setConnection();
        $this->fill($fillable);
        $this->syncOriginalAttributes();
    }

    protected function setConnection(): void
    {
        $this->connection = Container::getInstance()->make(DBConnection::class);
    }

    public function fill(?array $fillable): static
    {
        if (empty($fillable)) {
            return $this;
        }

        foreach ($fillable as $key => $value) {
            if ($this->isFillable($key, $value)) {
                $this->{$key} = $value;
            }
        }

        return $this;
    }

    public function syncOriginalAttributes(): void
    {
        foreach ($this->fillable as $key => $_) {
            $this->original[$key] = $this->{$key};
        }
    }

    protected function isFillable(string $key, mixed $value): bool
    {
        return array_key_exists($key, $this->fillable) && $this->fillable[$key] === gettype($value);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function exists(): bool
    {
        return $this->id !== null;
    }


    public function save(): self
    {
        $fields = $this->getFields();
        $this->id = $this->connection->insertGetId(
            $this->getInsertClause($fields),
            $fields
        );

        return $this;
    }

    protected function getInsertClause(array $fields): string
    {
        $columns = implode(', ', array_keys($fields));
        $values = collect($fields)
            ->keys()
            ->map(fn(string $field): string => ":$field")
            ->implode(', ');
        $table = $this->getTable();
        return "INSERT INTO $table ($columns) values ($values);";
    }

    protected function getTable(): string
    {
        if (isset($this->table)) {
            return $this->table;
        }

        return Str::of(static::class)
            ->afterLast('\\')
            ->lower()
            ->plural()
            ->__toString();
    }

    public function getFields(): array
    {
        return collect($this->fillable)
            ->flatMap(fn(string $cast, string $fillable): array => [$fillable => $this->cast($fillable)])
            ->all();
    }

    protected function cast(mixed $fillable): mixed
    {
        return match ($this->fillable[$fillable]) {
            'bool' => $this->{$fillable} ? 1 : 0,
            'string' => (string)$this->{$fillable},
            'int'|'integer' => (int)$this->{$fillable}
        };
    }

    public static function make(array $fields): static
    {
        return new static($fields);
    }
}
