#!/usr/bin/env bash

if [ "$#" -ne 1 ]; then
    echo "Использование: $0 <file_name>"
    exit 1
fi

FILE_NAME="$1"

if [[ ! -f "$FILE_NAME" ]] || [[ ! -s "$FILE_NAME" ]]; then
    echo "Ошибка: Файл не найден или пуст."
    exit 1
fi

echo "--- Анализ через Pure Bash + Coreutils ---"

tail -n +2 "$FILE_NAME" | tr -s ' ' | cut -d' ' -f3 | sort | uniq -c | sort -rn | head -n 3

echo "----------------------------------------"
