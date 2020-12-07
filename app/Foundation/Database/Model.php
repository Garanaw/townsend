<?php declare(strict_types = 1);

namespace App\Foundation\Database;

use App\Foundation\Container;
use Carbon\Carbon;
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

    public function fill(?array $fillable = []): static
    {
        if (empty($fillable)) {
            return $this;
        }

        foreach ($fillable as $key => $value) {
            if ($this->isFillable($key)) {
                $this->{$key} = $this->cast($key, $value);
            }
        }

        return $this;
    }

    public function syncOriginalAttributes(): void
    {
        foreach ($this->fillable as $key => $_) {
            if (isset($this->{$key})) {
                $this->original[$key] = $this->{$key};
            }
        }
    }

    protected function isFillable(string $key): bool
    {
        return array_key_exists($key, $this->fillable);
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
        return $this->getId() !== null;
    }

    public function save(): self
    {
        $fields = $this->getFields();
        $table = $this->getTable();

        if ($this->exists() === false) {
            $this->id = $this->connection->insertGetId($table, $fields);
            $this->syncOriginalAttributes();
            return $this;
        }

        if ($this->isDirty()) {
            $this->connection->update(
                $table,
                $fields,
                ['id' => $this->getId()]
            );
            $this->syncOriginalAttributes();
        }

        return $this;
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
            ->flatMap(fn(string $cast, string $fillable): array => [$fillable => $this->unCast($fillable)])
            ->all();
    }

    public function isDirty(): bool
    {
        foreach ($this->original as $field => $value) {
            if (isset($this->{$field}) && $this->{$field} !== $value) {
                return true;
            }
        }
        return false;
    }

    public function first(array $where): ?static
    {
        $model = $this->connection->get($this->getTable(), $where)->first();
        return $model
            ? self::make($model)->setId((int)$model['id'])
            : null;
    }

    protected function cast(string $fillable, mixed $value): mixed
    {
        return match ($this->fillable[$fillable]) {
            'bool' => (bool)$value,
            'string' => (string)$value,
            'int','integer' => (int)$value,
            'carbon' => Carbon::parse($value),
        };
    }

    protected function unCast(mixed $fillable): mixed
    {
        return match ($this->fillable[$fillable]) {
            'bool' => $this->{$fillable} ? 1 : 0,
            'string' => (string)$this->{$fillable},
            'int','integer' => (int)$this->{$fillable},
            'carbon' => $this->{$fillable}->toDateTimeString(),
        };
    }

    public static function make(array $fields): static
    {
        return new static($fields);
    }
}
