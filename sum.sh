#!/bin/bash

if [ "$#" -ne 2 ]; then
    echo "Usage: <param1> <param2>"
    exit 1
fi

num1="$1"
num2="$2"

number_regex='^-?[0-9]+([.][0-9]+)?$'

if ! [[ $num1 =~ $number_regex ]] || ! [[ $num2 =~ $number_regex ]]; then
    echo "Ошибка: оба аргумента должны быть числами."
    exit 1
fi

sum=$(awk "BEGIN { print $num1 + $num2 }")

echo "$sum"