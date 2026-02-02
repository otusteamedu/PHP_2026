FROM mysql:8.4.1

COPY ./docker/DB/sql/*.sql /docker-entrypoint-initdb.d/
COPY ./docker/DB/sql/z_set_grants_on_db.sh /docker-entrypoint-initdb.d/
