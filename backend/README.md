# Laravel Backend API

This is the Laravel backend API for the ByteSupply application.

## Structure

This backend serves as a REST API only. All frontend views have been moved to the Nuxt.js frontend.

## Setup

### 1. Install Dependencies
```bash
composer install
```

### 2. Configure Environment
Copy `.env.example` to `.env` and configure:
- Database connection
- API settings
- Sanctum configuration

### 3. Run Migrations
```bash
php artisan migrate --seed
```

### 4. Start Server
```bash
php artisan serve --port=8001
```

The API will be available at: `http://127.0.0.1:8001/api`

## API Routes

All API routes are defined in `routes/api.php`.

### Authentication
- `POST /api/register` - Register new buyer
- `POST /api/login` - Buyer login
- `POST /api/admin/login` - Admin login
- `GET /api/get-user` - Get current user (protected)
- `POST /api/logout` - Logout (protected)

### Resources
- `/api/categories` - Categories CRUD
- `/api/suppliers` - Suppliers CRUD
- `/api/hardware-components` - Hardware components CRUD
- `/api/orders` - Orders CRUD
- `/api/buyers` - Buyers CRUD
- `/api/admins` - Admins CRUD (admin only)
- `/api/selected-components` - Selected components

## Database

Database name: `bytesupply2`

## Frontend Connection

The frontend (Nuxt.js) connects to this API at:
- Base URL: `http://127.0.0.1:8001/api`
- Authentication: Bearer tokens (Laravel Sanctum)

## Notes

- This backend no longer serves Blade views
- All web routes (`routes/web.php`) are deprecated
- Only API routes are active
- CORS is configured for `localhost:3000` (Nuxt.js frontend)

