**To spin up Docker containers:**
```bash
docker compose up --build -d
```

Before creating a request, run the command to obtain a `refresh_token`, which will be saved in Redis:
```bash
php artisan app:init-command {code}
```

The `code` parameter is generated when creating a Self Client (https://api-console.zoho.eu/). It is required to retrieve the `refresh_token` once.

In `.env`, specify `ZOHO_CLIENT_ID` and `ZOHO_CLIENT_SECRET` for the account used to test the task.
