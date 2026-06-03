# JarvishStack

**JarvishStack** is a lightweight PHP MVC framework built on Symfony components and Twig templating. It gives you a clean, organised structure — routing, controllers, models, templating, database migrations, and a CLI scaffolding tool — without the overhead of a full-stack framework like Laravel.

**Features**
- MVC architecture
- Symfony-powered routing
- Twig templating engine
- Database migrations with tracking
- SMTP email via PHPMailer
- CLI commands for scaffolding controllers and migrations

---

## Requirements

- PHP **8.2** or higher
- Composer
- MySQL / MariaDB
- Optional: [Lando](https://lando.dev) for containerised local dev

---

## Setup

### 1. Clone and install

```bash
git clone https://github.com/prashantj4505/JarvishStack.git
cd JarvishStack
composer install
```

### 2. Configure environment

Copy the example and fill in your values:

```bash
cp .env.example .env
```

```dotenv
APP_ENV=dev

DB_HOST=localhost
DB_USER=root
DB_PASS=
DB_NAME=jarvishstack

SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USER=your_email@example.com
SMTP_PASS=your_smtp_password
SMTP_FROM=your_email@example.com
SMTP_SECURE=tls
```

Set `APP_ENV=prod` for production (enables Twig cache, hides error details).

### 3. Run database migrations

```bash
./jarvish jarvish:migrate
```

Migrations are tracked so re-running this command is safe — already-executed migrations are skipped.

### 4. Start the development server

```bash
php -S localhost:8000 -t public
```

Or with Lando:

```bash
lando start
```

---

## Default Routes

| Method | Path       | Description                        |
|--------|------------|------------------------------------|
| GET    | `/`        | Home page                          |
| GET    | `/users`   | Users listing                      |
| GET    | `/contact` | Contact form                       |
| POST   | `/contact` | Submit contact form                |
| GET    | `/contacts`| Contact submissions list           |
| GET    | `/docs`    | In-app documentation               |

---

## CLI Commands

### Scaffold a controller

```bash
./jarvish jarvish:make:controller
```

Prompts for a controller name and route path, then generates:
- `src/Controller/<Name>.php`
- `templates/<name>.html.twig`
- Route entry in `src/routes.php`

**Example:**

```
Controller name: HelloController
Route path:      /hello
```

### Create a migration

```bash
./jarvish jarvish:make:migration
```

Generates a migration class in `src/Migrations/` with a basic `up()` method.

### Run migrations

```bash
./jarvish jarvish:migrate
```

Runs all pending migrations and records executed ones in the `migrations` table.

### List all commands

```bash
./jarvish list
```

---

## Project Structure

```
JarvishStack/
├── public/               # Web root (entry point: index.php)
│   ├── index.php
│   ├── css/style.css
│   └── js/script.js
├── src/
│   ├── Controller/       # HTTP controllers (extend AbstractController)
│   ├── Model/            # Data models (extend AbstractModel)
│   ├── Service/          # Business logic (SmtpMailer, etc.)
│   ├── Database/         # DB connection singleton
│   ├── Migrations/       # Migration classes
│   ├── Command/          # CLI commands
│   └── routes.php        # Route definitions
├── templates/            # Twig templates
│   └── errors/           # 404 / 500 error pages
├── global.php            # dump() / dd() helpers
├── jarvish               # CLI entry point
└── .env.example          # Environment variable template
```

---

## Extending the Framework

- **New controller**: `./jarvish jarvish:make:controller`
- **New model**: Create a class in `src/Model/` extending `AbstractModel`
- **New migration**: `./jarvish jarvish:make:migration`, then edit the generated file
- **New service**: Create a class in `src/Service/`
- **Twig cache** (production): Create `var/cache/twig/` directory and set `APP_ENV=prod`

---

## License

MIT — see [LICENSE](LICENSE).
