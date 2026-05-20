# Security Operations

## Current WordPress Admin Policy

- Expected administrator: `admin <santosma@gmail.com>`
- Login URL: `/ingre`
- Direct `/wp-login.php`: blocked
- XML-RPC: blocked
- Application passwords: disabled unless explicitly re-enabled for a time-boxed automation task
- Anonymous `/wp-json/wp/v2/users`: blocked

## Recovery Rules

- Never commit production passwords, SSH passwords, application passwords, WooCommerce keys, or database credentials.
- Store operational credentials outside the repository.
- Rotate WordPress admin credentials after any suspicious login email.
- Revoke application passwords after automation runs.

## Repeatable Checks

Run:

```bash
scripts/audit/commerce-health.sh
scripts/audit/secret-scan.sh
scripts/audit/production-commerce-health.sh
```

Production checks should confirm:

- only expected administrators exist
- `/wp-login.php` returns 404
- `/xmlrpc.php` returns 403
- `/wp-json/wp/v2/users` returns 404 for anonymous users
- `/ingre` returns 200
