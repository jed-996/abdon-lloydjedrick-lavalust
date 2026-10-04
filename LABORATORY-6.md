# Laboratory Exercise No. 6

This repository contains both required parts of the exercise:

- **LavaLust API:** authenticated JSON endpoints under `/api`
- **React application:** source in `frontend/`, deployed under `/app/`

The browser communicates only with the LavaLust API. Aiven credentials stay in Render environment variables and are never included in the React bundle.

## Deployed links

- Application: <https://abdon-lloydjedrick-lavalust.onrender.com/app/>
- API base: <https://abdon-lloydjedrick-lavalust.onrender.com/api>
- GitHub API project: <https://github.com/jed-996/abdon-lloydjedrick-lavalust>
- GitHub React source: <https://github.com/jed-996/abdon-lloydjedrick-lavalust/tree/main/frontend>

## API endpoints

| Method | Route | Purpose |
|---|---|---|
| `POST` | `/api/auth/login` | Sign in and issue JWT tokens |
| `GET` | `/api/auth/me` | Read the authenticated user |
| `POST` | `/api/auth/refresh` | Rotate an access/refresh token pair |
| `POST` | `/api/auth/logout` | Revoke the refresh token |
| `GET` | `/api/products` | List products |
| `GET` | `/api/products/{id}` | Read one product |
| `POST` | `/api/products` | Add a product |
| `PUT` / `PATCH` | `/api/products/{id}` | Edit a product |
| `DELETE` | `/api/products/{id}` | Delete a product |

All product endpoints require `Authorization: Bearer <access_token>`.

## Run locally

1. Copy `.env.example` to `.env` and configure a local SQLite database or Aiven MySQL.
2. Create an administrator password hash with `php scripts/hash-password.php` and set `ADMIN_EMAIL` and `ADMIN_PASSWORD_HASH`.
3. Initialize the database and migrations:

   ```bash
   php lava jwt:generate
   php scripts/setup.php
   php lava migration run
   php lava migration status
   ```

4. Start LavaLust on port 8080:

   ```bash
   php -S 127.0.0.1:8080 -t public public/router.php
   ```

5. In another terminal, start React:

   ```bash
   cd frontend
   npm ci
   npm run dev
   ```

Open <http://127.0.0.1:5173/app/>.

## Migration commands

```bash
php lava migration status
php lava migration create-migration create_example_table
php lava migration run
php lava migration rollback
php lava migration rollback-all
php lava migration refresh
```

`rollback-all` and `refresh` remove application tables. Use them only with a disposable development database. Production web requests cannot invoke migration routes.

## Render and Aiven

Render builds the React app in a Node stage, copies it into `public/app`, and then starts the PHP/Apache service. On each deploy, `scripts/setup.php` creates missing tables without deleting data, seeds the API administrator from environment variables, and runs pending migrations.

Required Render variables are documented in `.env.example`. Keep `DB_PASSWORD`, `ADMIN_PASSWORD_HASH`, `APP_KEY`, `JWT_SECRET`, and `REFRESH_TOKEN_KEY` out of version control. Run `php lava jwt:generate` locally and set the generated keys in the Render service Environment settings before deploying. The API requires separate random values for both keys; replacing a key invalidates existing sessions.

## Submission screenshots

Capture these pages after deployment:

1. `/app/` login screen
2. Product inventory screen
3. Add product dialog
4. Edit product dialog or successful updated row
5. Delete confirmation and successful deletion
6. Aiven `defaultdb` tables showing `migrations`, `users`, `refresh_tokens`, and `products`

The existing authenticated database evidence page remains available at `/products/database-evidence` for the `products` table.
