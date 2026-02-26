<?php
// header('Content-Type: text/plain; charset=utf-8');
echo "Hello from PHP-FPM\n";
// phpinfo();

// Redis test
if (class_exists('Redis')) {
    $r = new Redis();
    $r->connect(getenv('REDIS_HOST') ?: 'redis', 6379);
    $r->set('test', 'ok');
    echo "Redis: " . $r->get('test') . "\n";
} else {
    echo "Redis extension not installed\n";
}

echo "<br>";
echo "<br>";

// Memcached test
if (class_exists('Memcached')) {
    $m = new Memcached();
    $m->addServer(getenv('MEMCACHED_HOST') ?: 'memcached', 11211);
    $m->set('test', 'ok');
    echo "Memcached: " . $m->get('test') . "\n";
} else {
    echo "Memcached extension not installed\n";
}

echo "<br>";
echo "<br>";

echo "DB_HOST из getenv: " . getenv('DB_HOST') . "<br>";
echo "DB_HOST из SERVER: " . ($_SERVER['DB_HOST'] ?? 'не найден') . "<br>";
echo "DB_HOST из ENV: " . ($_ENV['DB_HOST'] ?? 'не найден') . "<br>";

echo "<br>";
echo "<br>";
// Параметры подключения
// Если запускаете скрипт из Windows (хост) -> используйте '127.0.0.1'
// Если запускаете внутри Docker сети -> используйте имя сервиса 'db'
$host    = getenv('DB_HOST') ?: 'db';
$db      = 'app';
$user    = 'app';
$pass    = 'apppassword';
$charset = 'utf8mb4';


$dsn     = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    // PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    // PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    // PDO::ATTR_EMULATE_PREPARES   => false,
];

try
{
    $pdo = new PDO($dsn, $user, $pass, $options);
    echo "✅ Успешное подключение к базе данных!<br><br>";

    // 1. Создаем таблицу, если её нет
    $sqlCreate = "CREATE TABLE IF NOT EXISTS test_users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sqlCreate);
    echo "🔹 Таблица 'test_users' проверена/создана.<br>";

    // 2. Очистим и вставим тестовые данные (для наглядности)
    $pdo->exec("TRUNCATE TABLE test_users");
    $stmt = $pdo->prepare("INSERT INTO test_users (name) VALUES (?)");
    foreach (['Alice', 'Bob', 'Charlie'] as $name)
    {
        $stmt->execute([$name]);
    }
    echo "🔹 Тестовые данные добавлены.<br><br>";

    // 3. Вывод данных на экран
    echo "<strong>Результаты из таблицы:</strong><br>";
    $query = $pdo->query("SELECT id, name, created_at FROM test_users");

    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Name</th><th>Created At</th></tr>";
    while ($row = $query->fetch())
    {
        echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['name']}</td>
                <td>{$row['created_at']}</td>
              </tr>";
    }
    echo "</table>";

}
catch (\PDOException $e)
{
    echo "❌ Ошибка подключения: " . $e->getMessage();
}


// phpinfo();


