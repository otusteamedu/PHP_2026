<?php

abstract class ActiveRecord
{
    protected static PDO $pdo;

    public static function setConnection(PDO $pdo): void
    {
        static::$pdo = $pdo;
    }

    abstract public static function tableName(): string;

    abstract public function toArray(): array;

    abstract public static function fromArray(array $data): static;

    public function save(): bool
    {
        $data = $this->toArray();

        if (isset($data['id']) && $data['id'] !== null) {
            // UPDATE
            $fields = array_keys($data);
            $set = implode(', ', array_map(fn($f) => "$f = :$f", $fields));

            $sql = "UPDATE ".static::tableName()." SET $set WHERE id = :id";
            $stmt = static::$pdo->prepare($sql);

            return $stmt->execute($data);
        }

        // INSERT
        unset($data['id']);
        $fields = array_keys($data);
        $placeholders = array_map(fn($f) => ":$f", $fields);

        $sql = "INSERT INTO ".static::tableName().
               " (".implode(', ', $fields).") VALUES (".implode(', ', $placeholders).")";

        $stmt = static::$pdo->prepare($sql);
        $result = $stmt->execute($data);

        if ($result) {
            $this->id = (int)static::$pdo->lastInsertId();
        }

        return $result;
    }

    public function delete(): bool
    {
        if (!isset($this->id)) {
            return false;
        }

        $stmt = static::$pdo->prepare(
            "DELETE FROM ".static::tableName()." WHERE id = :id"
        );

        return $stmt->execute(['id' => $this->id]);
    }

    public static function find(int $id): ?static
    {
        $stmt = static::$pdo->prepare(
            "SELECT * FROM ".static::tableName()." WHERE id = :id"
        );

        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ? static::fromArray($row) : null;
    }

    public static function all(): array
    {
        $stmt = static::$pdo->query("SELECT * FROM ".static::tableName()." ORDER BY id DESC");

        $items = [];
        while ($row = $stmt->fetch()) {
            $items[] = static::fromArray($row);
        }

        return $items;
    }
}