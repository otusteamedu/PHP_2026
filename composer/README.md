# Основы работы с пакетами composer

### Основная задача
Необходимо создать свой пакет.

**Критерии оценки**
- Пакет должен ставиться при помощи composer require package-name
- Пакет должен отвечать PSR-4
- Пакет может подключаться в Composer либо с packagist, либо из GitHub

------------

### Результат

------------

**Установить пакет в проект**

 - composer require a.emelyanenko/bracket-validator

 **Подключить пакет в проект**

 ```
use Emelyanenko\BracketValidator\Service\BracketValidatorService;
use Emelyanenko\BracketValidator\Exceptions\BracketException;
```

**Пример использования**

```
$service = new BracketValidatorService();

header('Content-Type: application/json; charset=utf-8');

try {
    $input = $_POST['string'] ?? '';
    $service->validate($input);

    http_response_code(200);
    echo "200 OK: Всё хорошо";

} catch (InvalidBracketsException $e) {
    http_response_code(400);
    echo "400 Bad Request: " . $e->getMessage();

} catch (Throwable $e) {
    http_response_code(500);
    echo "500 Internal Server Error";
} 
 ```

Ссылка на git [Проект](https://github.com/Jony2Good/bracket-validator)