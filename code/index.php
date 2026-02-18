<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Работа по курсу Otus PHP Professional Developer</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 40px;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f9f9f9;
        }
        h1 {
            color: #2c3e50;
            text-align: center;
        }
        p {
            margin-bottom: 10px;
        }
        .filename {
            font-family: 'Courier New', monospace;
            background-color: #ecf0f1;
            padding: 2px 5px;
            border-radius: 4px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Работа по заданию №4</h1>
        <h2>Курс: Otus PHP Developer. Professional</h2>

        <p>Данная страница представляет собой отчёт по выполнению четвёртого задания курса <strong>PHP Developer. Professional</strong>.</p>

        <p>В рамках этого задания была проделана работа:</p>
        <ol>
            <li>Развернуты контейнеры
                <ul>
                    <li>Развернута виртуальная машина Ubuntu в Oracle VirtualBox</li>
                    <li>Nginx с балансировкой на два других nginx</li>
                    <li>Nginx второго порядка имеют балансировку на два контейнера с php-fpm</li>
                    <li>Контейнеры с php-fpm имеют доступ к общей папке с проектом на локальной машине</li>
                    <li>Контейнеры с php-fpm обращаются к контейнеру с Redis для хранения сессий</li>
                </ul>
            </li>
            <li>Описан файл string_validation.php валидирующий строку с круглыми скобками            
                <ul>
                    <li>Проверка на наличие параметра string в запросе</li>
                    <li>Проверка на пустоту</li>
                    <li>Проверка наличия сторонних символов</li>
                    <li>Проверка чтобы закрытых скобок не стало больше чем открытых</li>
                    <li>Проверка чтобы открытых скобок не было больше чем закрытых</li>
                    <li>Если кол-во скобок равно, то строка корректна</li>
                    <li>Отдача результата если валидировать строку не получилось</li>
                </ul>
            </li>
        </ol>

        <p>Пример задания с реализацией валидации строки находится в файле:</p>
        <p class="filename">string_validation.php</p>

        <hr>

        <footer>
            <p><small>© 2026 | Otus PHP Professional Developer | Задание №4</small></p>
        </footer>
    </div>
</body>
</html>
