#!/bin/bash

main() {
  read -p "Первое слагаемое: " a
  if ! validate "$a"; then
    echo "Не число '$a'">&2
    exit 1
  fi

  read -p "Второе слагаемое: " b
  if ! validate "$b"; then
    echo "Не число '$b'">&2
    exit 1
  fi
  result=$(calculate_sum "$a" "$b")

  echo "Сумма: $result"
}

calculate_sum() {
  local a="$1"
  local b="$2"

  echo "$a + $b" | bc
}

validate() {
  local value="$1"
  if [[ $value =~ ^-?[0-9]+(\.[0-9]+)?$ ]]; then
    return 0
  else
    return 1
  fi
}

main "$@"
