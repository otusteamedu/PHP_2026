#!/usr/bin/env bash

if [ "$#" -ne 2 ]; then
    echo "Ошибка: Нужно передать ровно два числа через пробел."
    exit 1
fi

RE='^-?[0-9]+([.][0-9]+)?$'

for arg in "$1" "$2"; do
    if [[ ! $arg =~ $RE ]]; then
        echo "Ошибка: '$arg' не является числом."
        exit 1
    fi
done

echo "Сумма:"
awk "BEGIN {print $1 + $2}"
