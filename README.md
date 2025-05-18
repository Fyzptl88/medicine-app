<!--
    This is the README file for the "medicine-app" project.
    Use this file to provide an overview of the project, setup instructions, usage guidelines, and any other relevant documentation for contributors and users.
-->

# Medicine App

A web application for searching medicines, built with **Laravel 10** and **Tailwind CSS**.

## Features

- Search for medicines (top 5 results displayed)
- User registration and authentication
- Rate limiting (20 searches per user)
- Responsive UI with Tailwind CSS

## Getting Started

### Prerequisites

- PHP >= 8.1
- Composer
- Node.js & npm
- MySQL or compatible database

### Installation

1. **Clone the repository:**
   ```bash
   git clone 'https://github.com/Fyzptl88/medicine-app.git'
   cd medicine-app
   ```

2. **Install PHP dependencies:**
   ```bash
   composer install
   ```

3. **Set up environment variables:**
   - Copy `.env.example` to `.env` and update database credentials.

4. **Run database migrations:**
   ```bash
   php artisan migrate
   ```

5. **Start the Laravel development server:**
   ```bash
   php artisan serve
   ```

6. **Install frontend dependencies and build assets:**
   Open a new terminal and run:
   ```bash
   npm install
   npm run dev
   ```

## Usage

- Register a new user or log in with an existing account.
- Use the search bar to find medicines (top 5 results will be shown).
- Note: Search rate limit is set to 20 requests per user.

## Running Tests

To run all tests:
```bash
php artisan test
```

To run a specific test (e.g., `DrugSearchTest`):
```bash
php artisan test --filter=DrugSearchTest
```