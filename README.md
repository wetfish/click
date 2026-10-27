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

By hand, once click has been added, so its tables are new and empty.

Older servers kept click's posts in a latin1 table, but the bytes in it are UTF-8, because click always sent UTF-8.
So export only the posts, with their bytes unchanged, on the old server:

```bash
mariadb-dump --default-character-set=latin1 --skip-set-charset --no-create-info --complete-insert --no-autocommit <database> Pages > click-pages.sql
```

Then import them into click's utf8mb4 table, from the server's clone of wetfish/server:

```bash
{ echo "SET NAMES utf8mb4;"; cat click-pages.sql; } | ./database.sh import click
```

`SET NAMES utf8mb4` tells MariaDB the bytes are UTF-8, which keeps every character as it was, emoji included.
A normal dump wouldn't work: it brings back the old latin1 table, and turns each byte of a character into a character of its own, so `é` would become `Ã©`.

The import is one transaction. If a post isn't valid UTF-8, MariaDB stops with "Incorrect string value", and nothing is imported.
To find those posts, run this on the old server:

```sql
SELECT `ID`, `Page` FROM `Pages`
WHERE NOT (CAST(CONVERT(CAST(`Data` AS BINARY) USING utf8mb4) AS BINARY) <=> CAST(`Data` AS BINARY))
   OR NOT (CAST(CONVERT(CAST(`Page` AS BINARY) USING utf8mb4) AS BINARY) <=> CAST(`Page` AS BINARY));
```

To import again, empty the table first with `TRUNCATE Pages;` in `./database.sh shell click`.

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
