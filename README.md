# AErmolenko/hw15 - Паттерны проектирования

Демо интернет-ресторана фаст-фуда. Используются паттерны:

- **Abstract Factory** - `app/Factory/*` создаёт базовый продукт-прототип (бургер / сэндвич / хот-дог).
- **Decorator** - `app/Decorator/*` навешивает ингредиенты (салат, лук, перец, сыр) поверх продукта - по рецепту стратегии или по пожеланию клиента.
- **Observer** - `app/Observer/*` подписывается на статус приготовления (`CookingStatus`) и рассылает оповещения (`ClientNotifier`, `KitchenLogger`).
- **Proxy** - `app/Proxy/QualityControlCookProxy` оборачивает `RealCook` пре/пост-событиями: проверка ингредиентов до готовки, контроль качества после; при провале продукт утилизируется.
- **Strategy** - `app/Strategy/*` определяет, что готовить (фабрика + рецепт).
- **DI** - `app/Container/Container.php` связывает зависимости через Reflection. Bindings собираются в `app/Kernel.php`.

## Запуск

```bash
cp .env.example .env
composer install
php public/index.php

# или через docker
docker compose up -d
docker compose exec php php public/index.php
```

## Как менять заказ

Заказ собирается в `public/index.php`. Меняйте стратегию (что готовить) и список `extras` (пожелания клиента).

### Бургер с сыром и салатом по рецепту + лук и перец от клиента

```php
$order = new Order(
    strategy: $container->get(BurgerStrategy::class),
    extras: [Onion::class, Pepper::class],
);
```

```
Заказ собран:
  Название: Бургер
  Ингредиенты: булка, котлета, соус, сыр, салат, лук, перец
  Цена: 325.00 ₽
```

### Сэндвич с сыром по рецепту + салат от клиента

```php
use App\Strategy\SandwichStrategy;
use App\Decorator\Salad;

$order = new Order(
    strategy: $container->get(SandwichStrategy::class),
    extras: [Salad::class],
);
```

```
Заказ собран:
  Название: Сэндвич
  Ингредиенты: тост, ветчина, масло, сыр, салат
  Цена: 230.00
```

### Хот-дог с луком по рецепту + перец от клиента

```php
use App\Strategy\HotDogStrategy;
use App\Decorator\Pepper;

$order = new Order(
    strategy: $container->get(HotDogStrategy::class),
    extras: [Pepper::class],
);
```

```
Заказ собран:
  Название: Хот-дог
  Ингредиенты: булочка, сосиска, горчица, лук, перец
  Цена: 175.00
```

### Базовый продукт без пожеланий

```php
$order = new Order(strategy: $container->get(HotDogStrategy::class));
```

## Пример лога готовки

```
[SMS клиенту] «Бургер» - Заказ принят
[SMS клиенту] «Бургер» - Подготовка ингредиентов
[SMS клиенту] «Бургер» - Готовится
[SMS клиенту] «Бургер» - Контроль качества
[SMS клиенту] «Бургер» - Готов к выдаче
```

Если `QualityControlCookProxy` забракует продукт (например, цена > 1000), последним статусом будет `Отбракован и утилизирован`, а `cook()` вернёт `null`.