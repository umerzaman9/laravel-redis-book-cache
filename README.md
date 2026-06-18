# Book Registry Platform (Laravel + Redis Cloud)

## Architectural Overview

The application utilizes a dual-layer Redis architecture to optimize database performance and deliver instant user updates:

1. **Read-Through Caching Layer** — Minimizes heavy MySQL execution queries. Book listings are cached directly in Redis Cloud with an automatic expiration lifecycle. Write actions automatically flush stale caches.
2. **Event-Driven Pub/Sub Pipeline** — When a new book is registered, Laravel acts as a **Publisher**, broadcasting a payload packet to a Redis channel. A dedicated background **Node.js Worker** acts as a permanent **Subscriber**, intercepts the event payload, and instantly beams it to active client UIs using **WebSockets (Socket.io)**.

```
┌─────────────┐   write   ┌─────────────┐   publish   ┌──────────────┐   emit   ┌─────────────┐
│   MySQL     │◄─────────►│   Laravel   │────────────►│ Redis Pub/Sub│─────────►│  Node.js     │
│ (source of  │  cache    │  (App +     │  channel    │   Channel    │ subscribe│  WebSocket   │
│  truth)     │◄─────────►│   Cache)    │             │              │          │  Bridge      │
└─────────────┘           └─────────────┘             └──────────────┘          └──────┬──────┘
                                                                                          │ socket.io
                                                                                          ▼
                                                                                   ┌─────────────┐
                                                                                   │  Browser(s) │
                                                                                   │  Live Feed  │
                                                                                   └─────────────┘
```

## 🛠️ System Requirements

Ensure you have the following installed on your local environment:

- PHP >= 8.2 (Laragon, Herd, or XAMPP)
- Composer
- Node.js & NPM
- A Redis Cloud account (or a local Redis server instance)

> **Laragon Users:** This project runs smoothly on **Laragon 6**. Laragon already bundles PHP, MySQL, and Node.js, so you can skip separate installs for those. Just make sure the bundled PHP version meets the `>= 8.2` requirement (switch versions via Laragon's **Quick App** menu if needed), and run all `composer`, `npm`, and `artisan` commands from Laragon's built-in **Terminal/Cmder** so they pick up the correct PHP and Node binaries automatically.

---

## Installation & Setup

### 1. Clone & Install PHP Dependencies

```bash
git clone https://github.com/umerzaman9/laravel-redis-book-cache.git redis-books-cache
cd redis-books-cache
composer install
```

### 2. Install Node.js Dependencies

```bash
npm install
```

### 3. Configure Environment Variables (`.env`)

Create a `.env` file in your root folder and ensure your MySQL and Redis Cloud credentials are accurately mapped:

```env
APP_NAME=Laravel
APP_ENV=local
APP_KEY=  # Generate with php artisan key:generate
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=redis_books
DB_USERNAME=root
DB_PASSWORD=

CACHE_DRIVER=redis

REDIS_CLIENT=predis
REDIS_HOST=your-redis-host.db.redis.io
REDIS_USERNAME=default
REDIS_PASSWORD=your-redis-password
REDIS_PORT=your-redis-port
```

### 4. Database & Cache Initialization

Run your migrations to generate your database structure, clear stale configuration caches, and initialize optimization matrices:

```bash
php artisan migrate
php artisan config:clear
php artisan optimize:clear
```

---

## Running the Application

To run the entire real-time application workspace, you must execute the following processes concurrently using separate terminal instances.

### Terminal 1: Laravel Web Server

```bash
php artisan serve
```

**App Platform Endpoint:** `http://127.0.0.1:8000`

> **Laragon shortcut:** if your project folder lives inside Laragon's `www` directory, Laragon's **Auto Virtual Hosts** feature can serve the app automatically (e.g. `http://redis-books-redis-cache.test`) without needing `php artisan serve` at all. Either approach works — just make sure you're consistent with whichever URL you use when testing the live feed below.

### Terminal 2: Node.js WebSocket Bridge (Redis Subscriber)

```bash
node server.js
```

**WebSocket Listener Endpoint:** `http://localhost:3000`

---

## Testing the Live Functionality

### 1. Verifying the Database Caching Layer

1. Open your browser log or terminal logs.
2. Load `http://127.0.0.1:8000`. The first boot will trigger a **Cache Miss**, loading details directly from MySQL and storing them in Redis.
3. Refresh the page. Subsequent requests register a **Cache Hit**, pulling data from Redis in milliseconds without touching MySQL.

### 2. Verifying the Real-Time Pub/Sub Live Activity Feed

1. Open your web browser and navigate to the home board layout: `http://127.0.0.1:8000`. Place this window on the left side of your screen.
2. Open a completely separate browser tab (or an Incognito window) and go to the creation layout: `http://127.0.0.1:8000/create`. Place this window on the right side of your screen.
3. Submit a new book form using the right window.
4. **The magic:** the exact millisecond the form is submitted, a real-time Bootstrap toast notification will smoothly slide onto the screen in the left window, displaying the newly registered book's details — without requiring any manual browser tab reloads!

---

## 📝 License

This project is open-sourced under the [MIT license](LICENSE).
