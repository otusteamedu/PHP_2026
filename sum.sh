if [ "$#" -lt 2 ]; then
  echo "No arguments supplied"
  exit 1
fi

for arg in "$@"; do
  if [[ ! "$arg" =~ ^-?[0-9]*$ ]]; then
    echo "$arg — это Не int"
	exit 1
  fi
done

sum=$(($1 + $2))

echo "$sum"