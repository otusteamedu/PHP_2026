<?php

declare (strict_types = 1);

namespace App\Infrastructure;

abstract class Model
{
    protected static string $table = '';
    protected array $attr          = [];
    protected array $relations     = [];

    public function __construct(array $attributes = [])
    {
        $this->fill($attributes);
    }

    protected function fill(array $attributes): void
    {
        foreach ($attributes as $key => $value) {
            if (empty($this->fillable) || in_array($key, $this->fillable) || $key === 'id') {
                $this->attr[$key] = $value;
            }
        }
    }

    protected static function getTable(): string
    {
        if (empty(static::$table)) {
            throw new \Exception("Свойство \$table не определено в классе " . static::class);
        }
        return static::$table;
    }

    public static function find(int $id)
    {
        $table = static::getTable();
        $stmt  = DB::connect()->prepare("SELECT * FROM {$table} WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch();
        return $data ? new static($data) : null;
    }

    public static function all()
    {
        $table = static::getTable();
        $stmt  = DB::connect()->prepare("SELECT * FROM {$table}");
        $stmt->execute();
        $rows = $stmt->fetchAll();
        return array_map(fn($row) => new static($row), $rows);
    }

    public static function where(string $field, mixed $value)
    {
        if (!preg_match('/^[a-zA-Z0-9_]{1,64}$/', $field)) {
            throw new \Exception("Недопустимое имя поля: " . htmlspecialchars($field));
        }

        $table = static::getTable();
        $stmt  = DB::connect()->prepare("SELECT * FROM {$table} WHERE {$field} = :value");
        $stmt->execute(['value' => $value]);
        $rows = $stmt->fetchAll();
        return array_map(fn($row) => new static($row), $rows);
    }

    public function __set($key, $value)
    {
        if (in_array($key, $this->fillable)) {
            $this->attr[$key] = $value;
        }
    }

    public function __get($key)
    {
        if (array_key_exists($key, $this->attr)) {
            return $this->attr[$key];
        }

        if (array_key_exists($key, $this->relations)) {
            return $this->relations[$key];
        }

        if (method_exists($this, $key)) {
            $this->relations[$key] = $this->$key();
            return $this->relations[$key];
        }

        throw new \Exception("Свойство '{$key}' не существует в модели " . static::class);
    }

    protected function hasMany(string $related, string $foreignKey, $localKey = 'id')
    {
        $value = $this->$localKey;

        return $related::where($foreignKey, $value);
    }

    protected function belongsTo(string $related, string $foreignKey, string $ownerKey = 'id')
    {
        $value = $this->$foreignKey;

        return $related::where($ownerKey, $value)[0] ?? null;
    }
}
