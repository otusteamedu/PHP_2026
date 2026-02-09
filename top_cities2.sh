#!/usr/bin/env bash

# 1. Валидация аргументов
if [ "$#" -ne 1 ]; then
    echo "Использование: $0 <file_name>"
    exit 1
fi

FILE_NAME="$1"

# 2. Проверка файла (существование и пустота)
if [[ ! -f "$FILE_NAME" ]] || [[ ! -s "$FILE_NAME" ]]; then
    echo "Ошибка: Файл не найден или пуст."
    exit 1
fi

echo "--- Анализ через Pure Bash + Coreutils ---"

# 3. Обработка без awk:
# tail -n +2       — пропускаем первую строку (хедер)
# tr -s ' '        — сжимаем лишние пробелы в один (для корректного cut)
# cut -d' ' -f3    — вырезаем 3-ю колонку (city)
# sort             — сортируем для работы uniq
# uniq -c          — считаем повторения
# sort -rn         — сортируем по количеству (desc)
# head -n 3        — забираем топ-3

tail -n +2 "$FILE_NAME" | tr -s ' ' | cut -d' ' -f3 | sort | uniq -c | sort -rn | head -n 3

echo "----------------------------------------"
