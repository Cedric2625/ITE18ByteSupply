# ByteSupply - Hardware Components E-Commerce System

A modern, full-stack e-commerce platform for managing and selling hardware components. Built with a separated frontend-backend architecture using Nuxt.js 3 and Laravel 12.

## 🚀 Features

- **Product Catalog & Search** - Browse and search hardware components with advanced filtering
- **Shopping Cart & Checkout** - Complete shopping experience with cart management
- **Order Management** - Track orders with real-time status updates and tracking numbers
- **Admin Dashboard** - Comprehensive admin interface for managing products, orders, and inventory
- **Role-Based Access Control** - Secure authentication with separate customer and admin interfaces
- **Inventory Management** - Real-time stock tracking and management
- **Responsive Design** - Works seamlessly on desktop, tablet, and mobile devices

## 🛠️ Technology Stack

### Frontend
- **Nuxt.js 3** - Vue.js framework with SSR support
- **Vue.js 3** - Progressive JavaScript framework
- **TypeScript** - Type-safe JavaScript
- **Tailwind CSS** - Utility-first CSS framework
- **Pinia** - State management for Vue
- **Font Awesome** - Icon library

### Backend
- **Laravel 12** - PHP web framework
- **Laravel Sanctum** - API authentication
- **Eloquent ORM** - Database abstraction layer
- **MySQL 8.0+** - Relational database

### Development Tools
- **Node.js 18+** & **npm** - Frontend package management
- **Composer** - Backend dependency management
- **Git** - Version control

## 📋 Prerequisites

Before you begin, ensure you have the following installed:

- **PHP 8.2 or higher** - [Download PHP](https://www.php.net/downloads.php)
- **Composer** - [Download Composer](https://getcomposer.org/download/)
- **Node.js 18+ and npm** - [Download Node.js](https://nodejs.org/)
- **MySQL 8.0+** - [Download MySQL](https://dev.mysql.com/downloads/mysql/)
- **Git** (optional) - [Download Git](https://git-scm.com/downloads)

## 🔧 Installation & Setup

### Step 1: Clone the Repository

```bash
git clone [your-repository-url]
cd example-app2
```

### Step 2: Backend Setup

1. **Navigate to backend directory:**
   ```bash
   cd backend
   ```

2. **Install PHP dependencies:**
   ```bash
   composer install
   ```

3. **Create environment file:**
   ```bash
   cp .env.example .env
   ```

4. **Generate application key:**
   ```bash
   php artisan key:generate
   ```

5. **Configure database in `.env` file:**
   
   Open `backend/.env` and update the following:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=bytesupply2
   DB_USERNAME=your_mysql_username
   DB_PASSWORD=your_mysql_password
   ```
   
   Replace `your_mysql_username` and `your_mysql_password` with your MySQL credentials.

6. **Create the database:**
   
   In MySQL, create the database:
   ```sql
   CREATE DATABASE bytesupply2;
   ```

7. **Run migrations and seeders:**
   ```bash
   php artisan migrate --seed
   ```
   
   This will create all database tables and populate them with sample data.

### Step 3: Frontend Setup

1. **Navigate to frontend directory:**
   ```bash
   cd ../frontend
   ```

2. **Install Node.js dependencies:**
   ```bash
   npm install
   ```

3. **Configure API base URL (optional):**
   
   Create `frontend/.env` file (optional, defaults are set in `nuxt.config.ts`):
   ```env
   NUXT_PUBLIC_API_BASE=http://127.0.0.1:8001/api
   ```

## 🚀 Running the Application

You need to run both the backend and frontend servers simultaneously.

### Terminal 1: Start Backend Server

```bash
cd backend
php artisan serve --port=8001
```

The backend API will be available at: **http://127.0.0.1:8001/api**

### Terminal 2: Start Frontend Server

```bash
cd frontend
npm run dev
```

The frontend application will be available at: **http://localhost:3000**

### Access the Application

- **Frontend:** http://localhost:3000
- **Backend API:** http://127.0.0.1:8001/api

## 📁 Project Structure

```
example-app2/
├── backend/                    # Laravel 12 Backend API
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/   # API controllers
│   │   │   ├── Middleware/    # Custom middleware
│   │   │   └── Requests/      # Form request validation
│   │   └── Models/            # Eloquent models
│   ├── database/
│   │   ├── migrations/        # Database migrations
│   │   └── seeders/           # Database seeders
│   ├── routes/
│   │   └── api.php            # API route definitions
│   └── .env                   # Environment configuration
│
└── frontend/                   # Nuxt.js 3 Frontend
    ├── pages/                 # Application pages (auto-routing)
    ├── components/            # Vue reusable components
    ├── composables/           # Vue composables (useApi, useAuth)
    ├── stores/               # Pinia state management
    ├── middleware/            # Route guards
    └── nuxt.config.ts         # Nuxt configuration
```

## 🔐 Default Credentials

After running the database seeder, check `backend/database/seeders/DatabaseSeeder.php` for default admin credentials. You can also register a new customer account through the registration page.

## 📡 API Endpoints

The backend provides RESTful API endpoints:

### Authentication
- `POST /api/register` - Register new customer
- `POST /api/login` - Customer login
- `POST /api/admin/login` - Admin login
- `POST /api/logout` - Logout
- `GET /api/get-user` - Get current user info

### Resources
- `GET /api/hardware-components` - List products (with search/filter)
- `GET /api/categories` - List categories
- `GET /api/suppliers` - List suppliers
- `GET /api/orders` - List orders
- `POST /api/orders` - Create order
- `PATCH /api/orders/{id}` - Update order (admin only)

For complete API documentation, see `backend/docs/api.md` or refer to `SYSTEM_PRESENTATION_GUIDE.md` section 9.3.

## 🛠️ Development Commands

### Backend Commands

```bash
cd backend

# Run migrations
php artisan migrate

# Run seeders
php artisan db:seed

# Clear cache
php artisan cache:clear

# Clear config cache
php artisan config:clear

# Start development server
php artisan serve --port=8001
```

### Frontend Commands

```bash
cd frontend

# Start development server
npm run dev

# Build for production
npm run build

# Preview production build
npm run preview
```

## 🔒 Security Features

- **Laravel Sanctum** - Token-based API authentication
- **Password Hashing** - Bcrypt encryption
- **CORS Protection** - Configured for allowed origins
- **Input Validation** - Server-side and client-side validation
- **SQL Injection Prevention** - Eloquent ORM with parameter binding
- **XSS Protection** - Vue.js automatic escaping
- **Role-Based Access Control** - Admin and buyer role separation

## 🐛 Troubleshooting

### Backend Issues

**Database Connection Error:**
- Verify MySQL is running
- Check database credentials in `backend/.env`
- Ensure database `bytesupply2` exists

**Port Already in Use:**
- Change port: `php artisan serve --port=8002`
- Update frontend API URL accordingly

**Migration Errors:**
- Drop and recreate database
- Run `php artisan migrate:fresh --seed`

### Frontend Issues

**API Connection Error:**
- Verify backend server is running on port 8001
- Check CORS configuration in `backend/config/cors.php`
- Verify API base URL in `frontend/nuxt.config.ts`

**Module Not Found:**
- Delete `node_modules` and `package-lock.json`
- Run `npm install` again

**Port Already in Use:**
- Change port in `nuxt.config.ts` or use: `npm run dev -- --port 3001`

## 📚 Documentation

- **System Presentation Guide:** `SYSTEM_PRESENTATION_GUIDE.md` - Complete system documentation
- **API Documentation:** `backend/docs/api.md` - API endpoint details
- **Backend README:** `backend/README.md` - Backend-specific documentation
- **Frontend README:** `frontend/README.md` - Frontend-specific documentation

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📝 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 👥 Authors

**ByteSupply Team**

## 🙏 Acknowledgments

- Laravel community
- Vue.js and Nuxt.js communities
- All contributors and open-source libraries used in this project

---

**System Version:** 1.0  
**Last Updated:** 2025-12-22

For detailed system documentation, please refer to `SYSTEM_PRESENTATION_GUIDE.md`.
