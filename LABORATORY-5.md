# Laboratory Exercise No. 5 — Stockroom

LavaLust product CRUD with session authentication, Aiven MySQL storage, and Render Docker deployment.

## Application

- `/login`: administrator sign-in using `ADMIN_EMAIL` and a PHP password hash in `ADMIN_PASSWORD_HASH`.
- `/products`: product list, total units, and stock indicators.
- `/products/create`: GET form; POST creates a product.
- `/products/edit/{id}`: GET form; POST updates a product.
- `/products/delete/{id}`: GET confirmation only; POST deletes a product.
- `/logout`: POST ends the session.

All product routes require authentication. Every mutation requires a session CSRF token. Values are validated on the server, database queries use bound parameters, displayed data is HTML escaped, and the session ID rotates on login. Existing student pages remain available at `/student`.

## Local preview (PHP 8.2+)

With PHP, PDO SQLite, PDO MySQL, and mbstring installed:

```sh
php scripts/local-setup.php
php -S 127.0.0.1:8080 -t public public/router.php
```

Open http://127.0.0.1:8080. The generated login details are in `runtime/local-access.txt`. The setup script refuses to overwrite an existing `.env`.

This preview uses SQLite only. Production explicitly requires MySQL; SQLite test results are not evidence of an Aiven connection.

Run the HTTP integration checks while the preview server is running:

```sh
python tests/integration.py
```

The checks create, edit, and remove their own local test product. They reject non-local URLs.

## Aiven MySQL

Use `database/products.sql` for the required products table. The application startup runs `php scripts/setup.php`, which creates it if missing and preserves existing rows.

Set `DB_HOST`, `DB_PORT`, `DB_USERNAME`, `DB_PASSWORD`, and `DB_NAME` (or the existing `DB_DATABASE` alias). The repository's existing `certs/ca.pem` is a public CA certificate, not a password. `DB_SSL_CA` can override its path. Certificate verification is required by default.

Connection configuration follows [Aiven's PHP connection documentation](https://aiven.io/docs/products/mysql/howto/connect-with-php).

## Render

Use the Docker runtime, repository root as the Docker context, and `Dockerfile`. The container serves only `public/` and binds Apache to Render's `PORT` (default 10000). See [Render's Docker documentation](https://render.com/docs/docker).

Required environment variables:

| Variable | Value |
| --- | --- |
| APP_ENV | production |
| APP_KEY | A generated random secret |
| ADMIN_EMAIL | Administrator email |
| ADMIN_PASSWORD_HASH | Output of `php scripts/hash-password.php` |
| DB_HOST / DB_PORT | Aiven connection host and port |
| DB_USERNAME / DB_PASSWORD | Aiven credentials |
| DB_NAME | defaultdb, or the selected database |
| DB_SSL_REQUIRED | true |
| DB_SSL_CA | /var/www/html/certs/ca.pem, or another readable CA path |

Render supplies `RENDER_EXTERNAL_URL`; `APP_URL` is an optional explicit override. Do not copy the local `.env` into Render. Set the health path to `/health.php`. This checks the web process; a successful authenticated product request verifies the database.

File sessions fit this single-instance laboratory deployment. A restart requires users to sign in again.

## Submission checklist

- GitHub repository: https://github.com/jed-996/abdon-lloydjedrick-lavalust
- Selected Render application: https://abdon-lloydjedrick-lavalust.onrender.com
- Screenshots: login, product list, add form, edit form, delete confirmation/success, and the Aiven products table.
- Verify signed-out access redirects to login and signed-in CRUD persists in Aiven.

Never publish `.env`, database passwords, plaintext sign-in passwords, or files from `runtime/`.
