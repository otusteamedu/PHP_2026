<?php
/** @var string $id */
/** @var string $email */
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>Запрос принят</title>
</head>
<body>
    <h1>Запрос принят в обработку</h1>
    <p>Идентификатор запроса: <strong><?= htmlspecialchars($id, ENT_QUOTES) ?></strong></p>
    <p>
        Готовая выписка будет отправлена на
        <strong><?= htmlspecialchars($email, ENT_QUOTES) ?></strong>
        по завершении обработки.
    </p>
    <p><a href="/">Заказать ещё одну выписку</a></p>
</body>
</html>