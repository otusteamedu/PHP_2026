#!/bin/bash

echo "Отладочная информация:"
echo "---------------------------"
echo "Имя скрипта: $0"
echo "Первый аргумент: $1"
echo "Все аргументы: $@"
echo "Количество аргументов: $#"

tail -n +2 "$1"
echo "---------------------------"

if [ $# -ne 1 ]; then
    echo "Использование: $0 <файл_с_таблицей>"
    exit 1
fi

if [ ! -f "$1" ]; then
    echo "Ошибка: файл $1 не найден"
    exit 1
fi

echo "Топ-3 самых популярных города:"
echo "---------------------------"

tail -n +2 "$1" | awk '{print $3}'| sort | uniq -c | sort -rn | head -3  | while read count city; do
    echo "$city: $count пользователей"
done
