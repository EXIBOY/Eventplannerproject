# Event Planner

Event Planner is a Laravel app for managing event briefs, schedules, and dashboards.

## Stack

- Laravel 12
- MySQL
- Vite
- Tailwind CSS

## MySQL setup

1. Create a MySQL database named `gatherly`.
2. Confirm your local MySQL credentials in `.env`.
3. Run the app setup:

```bash
composer install
npm install
php artisan key:generate
php artisan migrate --seed
npm run build
```

Default database settings in `.env.example`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gatherly
DB_USERNAME=root
DB_PASSWORD=
```

## Demo login

After seeding:

- Email: `planner@example.com`
- Password: `password`

## Development

Run the application locally with:

```bash
composer run dev
```

Or run the services separately:

```bash
php artisan serve
npm run dev
```

## Testing

Feature tests still use in-memory SQLite for speed and isolation:

```bash
php artisan test
```
