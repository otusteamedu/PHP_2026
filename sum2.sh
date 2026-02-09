#!/usr/bin/env bash

if ! command -v bc &> /dev/null; then
    echo "Утилита 'bc' не найдена. Попытка установки..."
    sudo apt update && sudo apt install -y bc
    if [ $? -ne 0 ]; then
        echo "Ошибка: не удалось установить bc. Попробуйте вручную: sudo apt install bc"
        exit 1
    fi
    echo "bc успешно установлен!"
fi

if [ "$#" -ne 2 ]; then
    echo "Ошибка: введите два числа. Пример: $0 1.5 -7"
    exit 1
fi

RE='^-?[0-9]+([.][0-9]+)?$'

for arg in "$1" "$2"; do
    if [[ ! $arg =~ $RE ]]; then
        echo "Ошибка: '$arg' не является валидным числом."
        exit 1
    fi
done

sum=$(echo "$1 + $2" | bc)

echo "Сумма чисел $1 и $2 составляет: $sum"
