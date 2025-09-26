# JarvishStack

**JarvishStack** is a lightweight PHP framework built using Symfony components and Twig, designed to give you a simple MVC structure, routing, templating, and built-in console commands for creating controllers and database migrations.  
It supports:

- MVC architecture
- Routing system
- Twig templating
- Console commands for creating controllers and migrations
- Database migrations system
- SMTP email sending via PHPMailer

JarvishStack is perfect for small to medium PHP projects where you want a clean structure without full-stack frameworks like Laravel.

---

## Prerequisites

- PHP **8.2** or above
- Composer installed
- MySQL or compatible database
- Optional: Lando (for containerized setup)

---

## Setup Steps

### 1. Clone the Repository

```bash
git clone https://github.com/prashant4505/JarvishStack.git
cd JarvishStack
```

---

### 2. Install PHP Dependencies

```bash
composer install
```

This installs all required packages such as Symfony, Twig, and PHPMailer.

---

### 3. Configure Environment

Create a `.env` file in the project root:

```dotenv
# Database configuration
DB_HOST=database
DB_NAME=jarvishstack
DB_USER=lamp
DB_PASS=lamp

# SMTP configuration
SMTP_HOST=smtp.example.com
SMTP_PORT=587
SMTP_USER=your_email@example.com
SMTP_PASS=your_smtp_password
SMTP_FROM=your_email@example.com
```
---

### 4. Run Database Migrations

Create default tables such as `contact_us`:

```bash
./jarvish jarvish:migrate
```

Future migrations can be added in `src/Migrations/` and rerun with the same command.

---

### 5. Start the Server

#### Option 1: PHP Built-in Server

```bash
php -S localhost:8000 -t public
```

#### Option 2: Lando

```bash
lando start
```

Access your application:

* `http://localhost:8000` (PHP server)
* `http://php.lndo.site` (Lando)

---

### 6. Access Default Features

* **Home page**: `/`
* **Users listing**: `/users`
* **Contact form**: `/contact` (submissions stored in `contact_us` table)
* **SMTP Email sending** via configured credentials

---

### 7. Creating Controllers

Generate new controllers and routes:

```bash
./jarvish jarvish:make:controller
```

It will ask for:

* Controller name (e.g., `HelloController`)
* Route path (e.g., `/hello`)

A new controller, twig template, and route entry will be automatically created.

---

### 8. Creating Migrations

Generate new migrations:

```bash
/.jarvish jarvish:make:migration
```

It will prompt for the table name and create a migration file in `src/Migrations/`.

Run all migrations:

```bash
./jarvish jarvish:migrate
```

---

JarvishStack is now ready for development, and you can expand it by creating new controllers, templates, and migrations as needed.

```

This includes:

- **Framework description**  
- **Prerequisites**  
- **All setup steps from clone → install → configure → migrate → run server**  
- **Instructions for controllers and migrations**  

I can also make a **visual, nicely formatted HTML documentation page** in Twig using Bootstrap that mirrors this README if you want the in-app docs. Do you want me to do that?
```
