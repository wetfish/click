# click that fish!

## How do I deploy this container stack?

See [https://github.com/wetfish/production-manifests](https://github.com/wetfish/production-manifests)
for production deployment and full stack dev env info.

For development, to run just this stack, do 
```bash
cp mariadb.env.example mariadb.env
# -> edit, change passwords and other info as needed
cp php.env.example php.env
# -> edit, change passwords to match

docker compose \
  -f docker-compose.dev.yml \
  up -d \
  --build \
  --force-recreate

docker compose -f docker-compose.dev.yml logs -f
```

The service will be available at [http://127.0.0.1:2401](http://127.0.0.1:2401)
Mysql db in dev env is reachable at tcp://127.0.0.1:3401
