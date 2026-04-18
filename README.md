## Поиск по магазину книг в Elasticsearch

Для запуска приложения выполните:
```bash
make init
```

Для поиска через Docker выполните:
```bash
docker-compose run --rm php-cli bin/search.php --help
```
```bash
docker-compose run --rm php-cli bin/search.php --query="рыцОри" --category="Исторический роман" --max-price=2000 --limit=25 --in-stock
```
или
```bash
make search ARGS="--help"
```
```bash
make search ARGS="--query='рыцОри' --limit=2"
```
