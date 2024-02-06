## Run project
```shell
docker-compose up -d
```

## Generate migrate
```shell
docker exec -i parser-symfony php bin/console make:migration -n
```
## Run migrations
```shell
docker exec -i parser-symfony php bin/console doctrine:migrations:migrate -n
```

## Run parse
```shell
docker exec -i parser-symfony php bin/console parser:exchange-rate -n
```