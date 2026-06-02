<?php

declare (strict_types = 1);

class PgSQLConnection implements DbConnectionInterface
{
    private ?PDO $pdo = null;

    public function connect(array $config): void
    {
        if ($this->pdo !== null) {
            return;
        }

        $dsn       = "pgsql:host={$config['host']};dbname={$config['db']};charset={$config['charset']}";
        $this->pdo = new PDO($dsn, $config['user'], $config['password'], 
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }

    public function execute(string $sql, array $bindings = []): array
    {
        if ($this->pdo === null) {
            throw new RuntimeException('Ошибка подключения к базе данных');
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($bindings);
        return $stmt->fetchAll();
    }

    public function getPdo(): PDO
    {
        return $this->pdo;
    }
}
