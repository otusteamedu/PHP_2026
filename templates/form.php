<?php /** @var string $error */ ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>Заказ банковской выписки</title>
</head>
<body>
    <h1>Заказ банковской выписки</h1>

    <?php if (!empty($error)): ?>
        <p><strong>Ошибка:</strong> <?= htmlspecialchars($error, ENT_QUOTES) ?></p>
    <?php endif; ?>

    <form method="post" action="/statements">
        <p>
            <label>Номер счёта:<br>
                <input type="text" name="account" required>
            </label>
        </p>
        <p>
            <label>Дата с:<br>
                <input type="date" name="date_from" required>
            </label>
        </p>
        <p>
            <label>Дата по:<br>
                <input type="date" name="date_to" required>
            </label>
        </p>
        <p>
            <label>Email для оповещения:<br>
                <input type="email" name="email" required>
            </label>
        </p>
        <p>
            <button type="submit">Заказать выписку</button>
        </p>
    </form>
</body>
</html>