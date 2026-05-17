## Часть интернет-ресторана, который продаёт фаст-фуд

### Соответствие ТЗ

1. Абстрактная фабрика отвечает за генерацию базового продукта-прототипа (бургер, сэндвич, хот-дог):  
   [src/Domain/Factory](src/Domain/Factory), [src/Application/Factory](src/Application/Factory)
2. Декоратор добавляет составляющие к базовому продукту по рецепту или по пожеланию клиента:  
   [src/Domain/Decorator](src/Domain/Decorator), [src/Domain/Configurator](src/Domain/Configurator), [src/Application/Configurator](src/Application/Configurator)
3. Наблюдатель подписывается на статус приготовления и отправляет оповещения об изменении статуса:  
   [src/Domain/Observer](src/Domain/Observer), [src/Application/Observer](src/Application/Observer)
4. Прокси навешивает pre/post события на процесс готовки (включая отбраковку/утилизацию при несоответствии стандарту):  
   [src/Domain/Proxy](src/Domain/Proxy), [src/Application/Proxy](src/Application/Proxy)
5. Стратегия отвечает за то, что именно нужно приготовить:  
   [src/Domain/Strategy](src/Domain/Strategy), [src/Application/Strategy](src/Application/Strategy)
6. Все сущности должны по максимуму генерироваться через DI:
   зависимости по максимуму собираются при конфигурации приложения — [bin/console](bin/console)

### Интерактивный тест в терминале

Для запуска консольного примера выполните:
1. Инициализация
```bash
make init
```
2. Запуск контроллера
```bash
make order
```

