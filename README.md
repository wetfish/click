# click that fish!

## How do I deploy this container stack?

See [https://github.com/wetfish/server](https://github.com/wetfish/server)
for setting up a server. Then add click to it with

```bash
./add-project.sh --name click --repo https://github.com/wetfish/click.git --domain <domain>
```

and it'll redeploy whenever `main` (staging) or `release` (production) is pushed.

Click keeps its posts in its own database on the server's shared MariaDB.
Adding click creates the database with empty tables from `schema.sql`, and fills in `php.env`, as `setup.conf` asks, so there's nothing to enter by hand.

## How do I restore an old database?

By hand, from the server's clone of wetfish/server, once click has been added.
Import a dump from the old server, which replaces the empty tables, then convert it to utf8mb4:

```bash
./database.sh import click < click.sql
./database.sh dump click > click-latin1.sql
./database.sh import click < projects/click/migrations/utf8mb4.sql
```

Older servers stored click's posts as latin1, which can't hold emoji.
`migrations/utf8mb4.sql` converts them to utf8mb4, keeping every character as it was, and the dump in between is a copy from before, just in case.
If any post isn't valid UTF-8, it stops without changing anything, and the file shows how to find those posts.
Running it again changes nothing.

For development, to run just this stack, do
```bash
cp mariadb.env.example mariadb.env
# -> edit, change passwords and other info as needed
cp php.env.example php.env
# -> edit, change passwords to match

docker compose up -d --build

docker compose logs -f
```

The service will be available at [http://127.0.0.1:2401](http://127.0.0.1:2401) \
MariaDB in the dev env is reachable at tcp://127.0.0.1:3401

The dev env runs its own MariaDB from `docker-compose.override.yml`, keeping its data in the `mariadb` Docker volume.
The first time it starts with an empty volume, it creates click's tables from `schema.sql`.

If you want to test it like it's on a real server, set up a
[local copy](https://github.com/wetfish/server/blob/main/docs/how-it-works.md#a-local-copy-for-testing)
of wetfish/server and add your clone by its path, with a domain like `click.localhost`. \
Heads up, it only deploys committed changes!

## When do I need to rebuild the container?

Whenever you make an edit in public_html or config. \
Edits to `schema.sql` only apply to a new, empty database.

The pages use [wetfish-basic](https://github.com/wetfish/basic), which the nginx image installs from `public_html/package.json`, so there's nothing to install locally.
