#!/bin/bash

if [ $# != 2 ]
then
  echo "The number of arguments must be equal to 2."
  exit 1
fi

if ! echo "$1" | grep -qE '^-?[0-9]+(\.[0-9]+)?$'
then
  echo "First argument must be number."
  exit 1
fi

if ! echo "$2" | grep -qE '^-?[0-9]+(\.[0-9]+)?$'
then
  echo "Second argument must be number."
  exit 1
fi

echo "$1 $2" | awk "{print $1 + $2}"

