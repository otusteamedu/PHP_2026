# 1. Абстрактная фабрика (Abstract Factory) (App\AbstractFactoryAndBuilder)

Сделано:

- Выделен интерфейсы ConnectionInterface, DatabaseFactoryInterface, QueryBuilderInterface и TransactionInterface;
- Реализованы классы для интерфейсов;
- conf настройки database, где при смене драйвера происходит переключение на другую БД.

Пример использования:

```php
use App\AbstractFactoryAndBuilder\DatabaseFactoryResolver;
$factory = DatabaseFactoryResolver::fromConfig($config);

$queryBuilder = $factory->createQueryBuilder();
$sql = $queryBuilder
    ->select(['users.id as user_id', 'posts.id as post_id'])
    ->from('users')
    ->where('users.id', '=', 1)
    ->join('posts', 'id', 'user_id')
    ->limit(10)
    ->offset(0)
    ->build();

$connection = $factory->createConnection();
$pdo = $connection->getConnection();
$transaction = $factory->createTransaction($connection);
$transaction->begin();

try {
    $result = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    $transaction->commit();
    var_dump($result);
} catch (\Exception $e) {
    $transaction->rollback();
    throw $e;
}
```

# 2. Шаблонный метод (Template Method) (App\Template)

Сделано:

- Создан абстрактный класс AbstractImporter;
- Реализованы классы CsvImporter, JsonImporter и XmlImporter;

Пример использования:

```php
use App\Template\Importers\CsvImporter;


$importer = new CsvImporter(__DIR__ . '/example.csv');
$importer->import();
```

```php
use App\Template\Importers\JsonImporter;


$importer = new JsonImporter(__DIR__ . '/example.json');
$importer->import();
```

```php
use App\Template\Importers\XmlImporter;

$importer = new XmlImporter(__DIR__ . '/example.xml');
$importer->import();
```

# 3. Строитель (Builder) (App\AbstractFactoryAndBuilder)

Сделано:

- Выделен интерфейс QueryBuilderInterface;
- Реализованы классы для интерфейса MySQLQueryBuilder и PostgreSQLQueryBuilder;

Пример использования:

```php
use App\AbstractFactoryAndBuilder\DatabaseFactoryResolver;
$factory = DatabaseFactoryResolver::fromConfig($config);

$queryBuilder = $factory->createQueryBuilder();
$sql = $queryBuilder
    ->select(['users.id as user_id', 'posts.id as post_id'])
    ->from('users')
    ->where('users.id', '=', 1)
    ->join('posts', 'id', 'user_id')
    ->limit(10)
    ->offset(0)
    ->build();

$connection = $factory->createConnection();
$pdo = $connection->getConnection();
$transaction = $factory->createTransaction($connection);
$transaction->begin();

try {
    $result = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    $transaction->commit();
    var_dump($result);
} catch (\Exception $e) {
    $transaction->rollback();
    throw $e;
}
```

# 4. Наблюдатель (Observer) (App\Observer)

Сделано:

- Выделен интерфейсы PublisherInterface и SubscriberInterface;
- Реализованы классы для интерфейса SubscriberInterface: EmailSender, Logger и UserBonus;
- Реализован Publisher;
- Реализован CreateUserUseCase, при вызове которого происходит создание User и вызывается event, который передается в Publisher для дальнейших действий.

Пример использования:

```php
use App\Observer\Publishers\Publisher;
use App\Observer\Subscribers\EmailSender;
use App\Observer\Subscribers\Logger;
use App\Observer\Subscribers\UserBonus;
use App\Observer\UseCase\CreateUserUseCase;

$publisher = new Publisher;
$publisher->subscribe(new EmailSender);
$publisher->subscribe(new Logger);
$publisher->subscribe(new UserBonus);

if (rand(0, 1) === 1) {
    $publisher->unsubscribe(new UserBonus);
}
$useCase = new CreateUserUseCase($publisher);
$useCase();
```

# 5. Стратегия (Strategy)

Сделано:

- Выделен интерфейс DeliveryInterface стратегии с методом getCost;
- Реализованы классы для интерфейса CourierDelivery, PickupDelivery и PostDelivery со своей логикой рассчета стоимости доставки;
- Стратегия внедряется через DI, что позволяет подменять стратегии не изменяя другой код, а также рассширять через создание других способов доставки.

Пример использования:

```php
$strategy = new PostDelivery;
$calculator = new DeliveryCalculator($strategy);
try {
    $price = $calculator->calculate($order);
} catch (\Exception $e) {
    echo "Исключение: {$e->getMessage()}";
    exit;
}
echo "{$order->getCustomer()}, стоимость доставки почтой заказа ({$order->getWeight()}кг.) до адреса {$order->getAddress()} составит: {$price->getAmount()}{$price->getCurrency()}" . PHP_EOL;
```

Пример выводимых данных:

```
Вася, стоимость доставки курьером заказа (49кг.) до адреса ул. Ленина, д. 10 составит: 10999₽
Вася, стоимость доставки самовывозом заказа (49кг.) до адреса ул. Ленина, д. 10 составит: 1₽
Вася, стоимость доставки почтой заказа (49кг.) до адреса ул. Ленина, д. 10 составит: 500.9₽
```
