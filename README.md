# Maraki Coffee House

Operations dashboard for **Maraki Coffee House** — daily sales, selectable expenses, profit, credit customers, menu, investments/loans, with Spatie RBAC and activity logging.

Built with **Laravel 13**, Blade, Tailwind CSS, Alpine.js.

## Features

- Responsive ops dashboard (mobile bottom nav + desktop sidebar)
- Daily sales from active menu (paid or credit)
- Daily expenses with selectable categories
- Profit by day (cash collected − expenses)
- Credit customers with unpaid totals and collections
- Menu catalog (activate/deactivate sellable items)
- Investments & loans (who, amount, paid/outstanding, repayment history)
- Admin-managed users, roles, permissions, expense categories
- Activity log on money and admin actions

## Brand

Ethiopian coffee ceremony identity: espresso ink, highland green, woven gold, jebena mark, Fraunces + DM Sans.

## Local setup

```bash
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate --seed
npm install
npm run build
php artisan serve
```

App URL: `http://localhost:8000`

### Seeded users (password: `password`)

| Email | Role |
|---|---|
| admin@maraki.local | Admin |
| manager@maraki.local | Manager |
| cashier@maraki.local | Cashier |

Public registration is disabled — admins create users.

## Tests

```bash
php artisan test --compact
```
