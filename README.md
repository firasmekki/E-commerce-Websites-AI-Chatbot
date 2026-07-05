# NextCommerce — E-commerce Platform with AI Chatbot

A full-featured Laravel 11 e-commerce application with a customer storefront, an admin back-office, and a Google Gemini-powered shopping assistant.

![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?logo=php&logoColor=white)
![Laravel](https://img.shields.io/badge/Laravel-11-FF2D20?logo=laravel&logoColor=white)
![License](https://img.shields.io/badge/license-MIT-blue)

## Overview

NextCommerce is a complete online store: product catalog, cart, coupons, checkout and order tracking for customers, plus a full admin dashboard to manage products, categories, orders, coupons and customer accounts. An AI chatbot (Google Gemini) is wired into the store's own data so it can answer questions about products, stock and orders in natural language.

# Aperçu

## Interface Administrateur

### Dashboard Admin
Vue d'ensemble du chiffre d'affaires, des commandes, des clients et du catalogue, avec graphiques d'évolution des revenus et de répartition des produits par catégorie.

**Capture :**
![Dashboard Admin](docs/screenshots/localhost_8000_admin_dashboard.png)

---

### Gestion des Produits
Liste du catalogue avec prix, stock et statut, création et modification de fiches produit.

**Capture :**
![Gestion des Produits](docs/screenshots/localhost_8000_admin_products.png)

**Capture :**
![Créer un produit](docs/screenshots/localhost_8000_admin_products-create.png)

**Capture :**
![Modifier un produit](docs/screenshots/localhost_8000_admin_products-edit.png)

---

### Gestion des Catégories
Organisation du catalogue en rayons/catégories de produits.

**Capture :**
![Gestion des Catégories](docs/screenshots/localhost_8000_admin_categories.png)

---

### Gestion des Comptes Clients
Suivi des comptes clients et de leur statut (actif/refusé).

**Capture :**
![Gestion des Comptes Clients](docs/screenshots/localhost_8000_admin_customers.png)

---

### Gestion des Commandes
Suivi et mise à jour du statut des commandes (en attente, livrée, etc.).

**Capture :**
![Gestion des Commandes](docs/screenshots/localhost_8000_admin_orders.png)

---

### Gestion des Coupons
Création et suivi des codes de réduction appliqués au panier.

**Capture :**
![Gestion des Coupons](docs/screenshots/localhost_8000_admin_coupons.png)

---

## Interface Client

### Connexion
Authentification sécurisée des clients.

**Capture :**
![Connexion](docs/screenshots/localhost_8000_client_login.png)

---

### Inscription
Création d'un nouveau compte client.

**Capture :**
![Inscription](docs/screenshots/localhost_8000_client_register.png)

---

### Dashboard Client
Vue d'ensemble du compte : commandes récentes, produits disponibles, nouveaux produits et accès rapide au panier.

**Capture :**
![Dashboard Client](docs/screenshots/localhost_8000_client_dashboard.png)

---

### Boutique
Catalogue des produits avec recherche et filtrage par catégorie.

**Capture :**
![Boutique](docs/screenshots/localhost_8000_client_products.png)

---

### Détail Produit
Fiche produit détaillée : description, prix, stock disponible, produits similaires et avis clients.

**Capture :**
![Détail Produit](docs/screenshots/localhost_8000_client_product-detail.png)

---

### Catégories
Liste des catégories de produits disponibles.

**Capture :**
![Catégories](docs/screenshots/localhost_8000_client_categories.png)

---

### Détail Catégorie
Produits filtrés par catégorie sélectionnée.

**Capture :**
![Détail Catégorie](docs/screenshots/localhost_8000_client_category-detail.png)

---

### Panier
Ajustement des quantités, application d'un code promo et validation de la commande.

**Capture :**
![Panier](docs/screenshots/localhost_8000_client_cart.png)

---

### Mes Commandes
Historique des commandes passées par le client.

**Capture :**
![Mes Commandes](docs/screenshots/localhost_8000_client_orders.png)

---

### Détail Commande
Consultation détaillée d'une commande.

**Capture :**
![Détail Commande](docs/screenshots/localhost_8000_client_order-detail.png)

---

### Facture
Facture téléchargeable pour une commande passée.

**Capture :**
![Facture](docs/screenshots/localhost_8000_client_order-invoice.png)

---

### Profil
Gestion des informations personnelles et du mot de passe.

**Capture :**
![Profil](docs/screenshots/localhost_8000_client_profile.png)

---

### Assistant IA
Chatbot intelligent (Google Gemini) répondant aux questions sur les produits, le stock et les commandes.

**Capture :**
![Assistant IA](docs/screenshots/localhost_8000_client_chatbot.png)

---

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
