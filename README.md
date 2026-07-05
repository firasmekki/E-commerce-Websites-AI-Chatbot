# E-commerce AI Chatbot Application

A complete Laravel 11 e-commerce application with an integrated AI chatbot powered by Google Gemini API. This project demonstrates modern web development practices including MVC architecture, service layer pattern, comprehensive testing, and intelligent AI integration.

## Features

- **Product Management**: Browse, search, and filter products by category
- **Order Management**: View order history and order details
- **Category Management**: Organize products into categories
- **AI Chatbot**: Intelligent conversational assistant integrated with business data
- **Authentication**: User registration and login via Laravel Breeze
- **Responsive Design**: Bootstrap-based frontend with modern UI
- **Comprehensive Testing**: Unit and feature tests with high code coverage

## Tech Stack

- **Backend**: Laravel 11, PHP 8.x
- **Frontend**: Blade templates, Bootstrap 5
- **Database**: SQLite (configurable for MySQL/PostgreSQL)
- **Authentication**: Laravel Breeze
- **AI Integration**: Google Gemini API
- **Testing**: PHPUnit
- **ORM**: Eloquent

## Installation

### Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js and NPM (for asset compilation)
- SQLite or MySQL/PostgreSQL

### Step 1: Clone the Repository

```bash
git clone <repository-url>
cd web2026pfa
```

### Step 2: Install Dependencies

```bash
composer install
npm install
```

### Step 3: Environment Configuration

Copy the example environment file:

```bash
cp .env.example .env
```

Generate application key:

```bash
php artisan key:generate
```

### Step 4: Configure AI API Key

Add your Google Gemini API key to the `.env` file:

```
GEMINI_API_KEY=your_gemini_api_key_here
```

To get a Google Gemini API key:
1. Visit [Google AI Studio](https://makersuite.google.com/app/apikey)
2. Create a new API key
3. Add it to your `.env` file

### Step 5: Database Setup

Run migrations:

```bash
php artisan migrate
```

Seed the database with sample data:

```bash
php artisan db:seed
```

### Step 6: Compile Assets

```bash
npm run build
```

### Step 7: Start Development Server

```bash
php artisan serve
```

The application will be available at `http://127.0.0.1:8000`

## Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── ProductController.php
│   │   ├── OrderController.php
│   │   ├── CategoryController.php
│   │   └── ChatbotController.php
│   └── Requests/
├── Models/
│   ├── User.php
│   ├── Product.php
│   ├── Category.php
│   ├── Order.php
│   ├── OrderItem.php
│   └── ChatHistory.php
├── Services/
│   ├── ProductService.php
│   ├── OrderService.php
│   └── ChatbotService.php
database/
├── migrations/
├── seeders/
└── factories/
resources/
└── views/
    ├── products/
    ├── orders/
    ├── categories/
    └── chatbot/
tests/
├── Unit/
└── Feature/
```

## Database Schema

### Tables

- **users**: User authentication and profiles
- **categories**: Product categories
- **products**: Product catalog
- **orders**: Customer orders
- **order_items**: Order line items
- **chat_history**: AI chatbot conversation history

### Relationships

- User has many Orders and ChatHistory
- Category has many Products
- Product belongs to Category, has many OrderItems
- Order belongs to User, has many OrderItems
- OrderItem belongs to Order and Product
- ChatHistory belongs to User

## API Endpoints

### Public Routes

- `GET /products` - List all products (with search and category filter)
- `GET /products/{id}` - Show product details
- `GET /categories` - List all categories
- `GET /categories/{id}` - Show category with products

### Protected Routes (Authentication Required)

- `GET /orders` - List user orders (with status filter)
- `GET /orders/{id}` - Show order details
- `POST /api/chatbot` - Send message to AI chatbot
- `GET /api/chatbot/history` - Get chat history

## AI Chatbot Integration

The chatbot is integrated with Google Gemini API and features:

- **Intelligent Prompt Construction**: Dynamically builds prompts with business data and conversation context
- **Business Data Integration**: Queries real product and order data for contextual responses
- **Conversation History**: Maintains conversation context for better user experience
- **Error Handling**: Graceful error handling with logging
- **Multi-language Support**: French language interface

### Chatbot Capabilities

- Product search and recommendations
- Order status inquiries
- Business statistics queries
- General product information

## Testing

### Run All Tests

```bash
php artisan test
```

### Run Unit Tests Only

```bash
php artisan test --testsuite=Unit
```

### Run Feature Tests Only

```bash
php artisan test --testsuite=Feature
```

### Generate Coverage Report

```bash
php artisan test --coverage
```

Coverage report will be generated in the `coverage/` directory.

### Test Coverage

- **Unit Tests**: Models, Services, and prompt construction
- **Feature Tests**: Routes, CRUD operations, authentication, and chatbot endpoints
- **Current Coverage**: 65 tests passing (125 assertions)

## Development

### Code Style

This project follows Laravel coding standards and PSR-12 coding style.

### Service Layer Pattern

Business logic is encapsulated in service classes:
- `ProductService`: Product-related business logic
- `OrderService`: Order-related business logic
- `ChatbotService`: AI integration and conversation management

### Security Best Practices

- Input validation using Form Requests
- CSRF protection enabled
- SQL injection prevention via Eloquent ORM
- XSS protection via Blade templating
- Authentication and authorization middleware

## Deployment

### Production Checklist

1. Set `APP_ENV=production` in `.env`
2. Set `APP_DEBUG=false` in `.env`
3. Configure production database
4. Set strong `APP_KEY`
5. Configure mail settings
6. Run `php artisan config:cache`
7. Run `php artisan route:cache`
8. Run `php artisan view:cache`
9. Set up SSL certificate
10. Configure web server (Nginx/Apache)

## Troubleshooting

### Common Issues

**Issue**: API key not working
- **Solution**: Verify your Google Gemini API key is valid and has sufficient quota

**Issue**: Database connection errors
- **Solution**: Check database credentials in `.env` file and ensure database server is running

**Issue**: Assets not loading
- **Solution**: Run `npm run build` to compile assets

**Issue**: Tests failing
- **Solution**: Run `php artisan migrate:fresh` and `php artisan db:seed` to reset database

## Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Write/update tests
5. Ensure all tests pass
6. Submit a pull request

## License

This project is open-sourced software licensed under the MIT license.

## Author

Developed as a Laravel 11 e-commerce application with AI chatbot integration.

## Acknowledgments

- Laravel Framework
- Google Gemini API
- Bootstrap CSS Framework
- Laravel Breeze
