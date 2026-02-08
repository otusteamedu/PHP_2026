#!/usr/bin/env bash

if [ "$#" -ne 1 ]; then
    echo "Ошибка: Укажите файл с данными."
    exit 1
fi

FILE_NAME="$1"

if [[ ! -f "$FILE_NAME" ]]; then
    echo "Ошибка: Файл '$FILE_NAME' не найден или это не файл!"
    exit 1
fi

if [[ ! -s "$FILE_NAME" ]]; then
    echo "Ошибка: Файл '$FILE_NAME' пуст."
    exit 1
fi

echo "--- Анализ файла: $FILE_NAME ---"
echo "Топ 3 самых популярных города:"

awk 'NR > 1 {print $3}' "$FILE_NAME" | sort | uniq -c | sort -rn | head -n 3

echo "--------------------------------"
