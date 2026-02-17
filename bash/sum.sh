#!/usr/bin/env bash

is_number() {
  [[ $1 =~ ^-?[0-9]+(\.[0-9]+)?$ ]]
}

if [[ $# -ne 2 ]]; then
  echo "Ошибка: требуются 2 аргумента"
  exit 1
fi

if ! is_number "$1" || ! is_number "$2"; then
  echo "Ошибка:два аргумента должны быть числами"
  exit 1
fi

awk -v n1="$1" -v n2="$2" 'BEGIN { print n1 + n2 }'