#!/bin/bash

# Argument validation check
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

sum=$(echo "$num1 + $num2" | bc)

echo "$sum"