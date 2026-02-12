# Book Finder

A REST API built with Laravel 12 that allows searching for books, managing favorites and import books from the Google Books API.

## Table of Contents

- [Introduction](#introduction)
- [Requirements](#requirements)
- [Installation & Setup](#installation--setup)
- [Project Structure](#project-structure)
- [API Endpoints](#api-endpoints)
- [Features](#features)

---

## Introduction

**Book Finder** is a backend API designed to provide books management. Users can:

- **Register and authenticate** using email and password
- **Search and browse** a collection of different books
- **Manage favorites** by adding or removing books from collection
- **Import books** from the Google Books API (Admin only)
- **Access API documentation** via Swagger/OpenAPI

---

## Requirements

### System Requirements

- **PHP**: > 8.2
- **Composer**: Latest version
- **Node.js**: > 18.0
- **NPM**: > 9.0
- **MySQL**

>[!NOTE]
> I personally used [Laravel Herd](https://herd.laravel.com/) to setup everything on my local environment (since you can enable multiple PHP versions and Node versions using NVM), and each local project will have a custom domain locally, something like `book-finder.test`

---

## Installation & Setup

### 1. Clone the Repository

```bash
git clone https://github.com/Boudissa-Ihab/Book-Finder.git
cd Book-Finder
```

### 2. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### 3. Environment Configuration

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

Edit `.env` file and configure the following:
- `APP_URL`: Application base URL (default: `http://localhost:8000`)
- update your own `DB_DATABASE`, `DB_USERNAME` and `DB_PASSWORD`
- create a database in your DBMS with the same name as your .env `DB_DATABASE`
- (optiona) personally, i changed `LOG_CHANNEL` to "daily" instead of "stack"

### 4. Database Setup

```bash
# Run migrations
php artisan migrate

# Seed the database with sample data
php artisan db:seed
```

### 5. Generate API Documentation

```bash
# Re-generate API documentation
php artisan l5-swagger:generate
```

### 7. Start the Development Server

```bash
php artisan serve
```

The application will be available at `http://localhost:8000`

---

## Project Structure

```
Book-Finder/
├── app/
│   ├── Console/
│   │   └── Commands/
│   │   │   └── GenerateAdmin.php        # Generate an admin
│   ├── Enums/
│   │   └── Roles.php            # User roles enumeration
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Controller.php   # Base controller with OpenAPI configuration
│   │   │   └── Api/
│   │   │       ├── Auth/
│   │   │       │   ├── LoginController.php
│   │   │       │   └── RegistrationController.php
│   │   │       └── Books/
│   │   │           ├── BookController.php
│   │   │           ├── FavoriteController.php
│   │   │           └── GoogleApiController.php
│   │   ├── Requests/
│   │   │   └── Api
│   │   │       ├── Auth        # Login & Register
│   │   │       └── Books       # Favorite & Google API
│   │   └── Resources/
│   │   │   └── BookResource.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Book.php
│   │   └── UserBook.php         # Pivot model for favorite books
│   ├── Providers/   
│   |   └── AppServiceProvider.php       # Defines API rate limit          
│   ├── Services/
│   │   ├── GoogleBookApiService.php
│   │   ├── GoogleBookDto.php            # Data Transfer Object
│   │   └── GoogleBookMapping.php        # Data mapping logic
│   └── Swagger/
│       └── Book.php             # Book schema definition
├── bootstrap/
│   └── app.php                  # Roles & Permissions middlewares
├── database/
│   ├── factories/
│   │   ├── BookFactory.php 
│   │   ├── UserBookFactory.php         
│   │   └── UserFactory.php
│   ├── migrations/
│   └── seeders/
├── routes/
│   └── api.php                  # API routes with Sanctum
└── storage/       
    └── api-docs       
        └── api-docs.json        # Generated API docs       
```

## API Endpoints

### Authentication

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/register` | Register a new user |
| POST | `/api/login` | Authenticate user and receive token |
| POST | `/api/logout` | Invalidate user session |

### Books

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| GET | `/api/books` | Get all books | Required |
| GET | `/api/favorites` | Get user favorite books | Required |
| POST | `/api/favorites/{book_id}` | Add book to favorites | Required |
| DELETE | `/api/favorites/{book_id}` | Remove book from favorites | Required |

### Google Books (Admin Only)

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| GET | `/api/google/books/view` | View Google Books raw data | Admin |
| GET | `/api/google/books/search` | Search books on Google Books API | Admin |
| POST | `/api/google/books/import` | Import books from Google Books | Admin |

### Documentation

| Endpoint | Description |
|----------|-------------|
| `/api/documentation` | Interactive Swagger UI for API documentation |

---

## Features

✅ User Registration & Authentication  
✅ Role-Based Access Control (RBAC)  
✅ View books from the local DB
✅ Favorite Books Management  
✅ Google Books API Integration  
✅ API Documentation (Swagger/OpenAPI)  
✅ Database Seeding with Factories  
✅ Error Handling & Validation  


## Assumptions

❓ `author` attribute was taken as-is fro; the test, neither `authors` attribute nor `authors_table` were used <br>
❓ For `isbn`, and since ISBN is divided to ISBN-13 and ISBN-10, i'm saving ISBN-13 to the database in my case (it's not found, we save ISBN-10) but in reality these 2 numbers should be in different columns <br>
❓ I used Google Books API endpoints without an API key nor an OAUTH2 access via Google Cloud Console. <br>
❓ For rate limiting, i've set it up to 100 attempts per minute which should be enough for testing. <br>


## Room for Improvement

⚠️ Add test cases for all current API endpoints <br>
⚠️ Add versioning to API endpoints (/v1, /v2 ...) <br>
⚠️ Finish documenting the remaining of API endpoints <br>
⚠️ Add indexes in database to speed up read operations <br>
⚠️ Add caching for frequently accessed books and queries <br>
⚠️ Better organization of Errors, Exceptions and Responses <br>
⚠️ For Google Books API, i didn't handle all query parameters (as-per their [documentation](https://developers.google.com/books/docs/v1/using)) <br>
⚠️ In case I want to add another external books API, need to integrate a strategy pattern for easier switch between these services

---
