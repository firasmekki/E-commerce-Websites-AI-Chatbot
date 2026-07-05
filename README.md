# NextCommerce — E-commerce Platform with AI Chatbot

A full-featured Laravel 11 e-commerce application with a customer storefront, an admin back-office, and a Google Gemini-powered shopping assistant.

![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?logo=php&logoColor=white)
![Laravel](https://img.shields.io/badge/Laravel-11-FF2D20?logo=laravel&logoColor=white)
![License](https://img.shields.io/badge/license-MIT-blue)

## Overview

NextCommerce is a complete online store: product catalog, cart, coupons, checkout and order tracking for customers, plus a full admin dashboard to manage products, categories, orders, coupons and customer accounts. An AI chatbot (Google Gemini) is wired into the store's own data so it can answer questions about products, stock and orders in natural language.

## Screenshots

> Add screenshots to `docs/screenshots/` and reference them below (see [Screenshots to capture](#screenshots-to-capture) for the recommended list).

| | |
|---|---|
| ![Home / product catalog](docs/screenshots/01-catalog.png) | ![Product detail](docs/screenshots/02-product-detail.png) |
| ![Cart & checkout](docs/screenshots/03-cart.png) | ![AI chatbot](docs/screenshots/04-chatbot.png) |
| ![Admin dashboard](docs/screenshots/05-admin-dashboard.png) | ![Order management](docs/screenshots/06-admin-orders.png) |

## Features

### Storefront
- Product catalog with search and category filtering
- Product detail pages with customer reviews
- Cart with quantity updates and coupon codes
- Checkout flow and order placement
- Order history, order detail and downloadable invoice
- Order cancellation
- Authentication and profile management (Laravel Breeze)

### Admin back-office
- Dashboard with store statistics
- Product management (CRUD)
- Category management (CRUD)
- Coupon management (CRUD)
- Order management and status updates
- Customer account approval/rejection
- Review moderation

### AI Chatbot
- Conversational assistant backed by the Google Gemini API
- Answers grounded in real product, stock and order data
- Persists conversation history per user
- French-language interface

## Tech Stack

- **Backend**: Laravel 11, PHP 8.2+
- **Frontend**: Blade templates, Bootstrap 5, Vite
- **Database**: MySQL (SQLite supported for local/dev use)
- **Authentication**: Laravel Breeze
- **AI Integration**: Google Gemini API
- **Testing**: PHPUnit

## Getting Started

### Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js and npm
- MySQL (or SQLite for a quick local setup)

### Installation

```bash
git clone https://github.com/firasmekki/E-commerce-Websites-AI-Chatbot.git
cd E-commerce-Websites-AI-Chatbot

composer install
npm install

cp .env.example .env
php artisan key:generate
```

### Configure the environment

Edit `.env` with your database credentials and add your Gemini API key:

```
DB_CONNECTION=mysql
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

GEMINI_API_KEY=your_gemini_api_key_here
```

Get a free Gemini API key from [Google AI Studio](https://makersuite.google.com/app/apikey).

### Database & assets

```bash
php artisan migrate
php artisan db:seed
npm run build
```

### Run the app

```bash
php artisan serve
```

Visit `http://127.0.0.1:8000`.

## Project Structure

```
app/
├── Http/Controllers/       # Storefront, Admin, Auth and Chatbot controllers
├── Models/                 # User, Product, Category, Order, OrderItem, Coupon, ChatHistory, ...
├── Services/                # ProductService, OrderService, ChatbotService
database/
├── migrations/
├── seeders/
└── factories/
resources/views/            # Blade views (storefront, admin, chatbot)
routes/web.php              # Route definitions
tests/
├── Unit/
└── Feature/
```

## Testing

```bash
php artisan test                       # all tests
php artisan test --testsuite=Unit      # unit tests only
php artisan test --testsuite=Feature   # feature tests only
```

## Security

- All secrets (API keys, database credentials, `APP_KEY`) live in `.env`, which is **not** committed — see `.env.example` for the required variables.
- CSRF protection, form request validation, Eloquent (parameterized queries) and Blade auto-escaping are used throughout to guard against CSRF/SQL-injection/XSS.
- Admin routes are gated behind authentication + authorization middleware.
- Before deploying: set `APP_ENV=production`, `APP_DEBUG=false`, generate a fresh `APP_KEY`, and serve over HTTPS.

## Deployment Checklist

1. `APP_ENV=production`, `APP_DEBUG=false`
2. Configure the production database and mail settings
3. `php artisan config:cache && php artisan route:cache && php artisan view:cache`
4. `npm run build`
5. Configure HTTPS and the web server (Nginx/Apache)

## License

Licensed under the [MIT License](LICENSE).

## Author

**Firas Mekki** — [@firasmekki](https://github.com/firasmekki)
