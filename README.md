# 🎓 EduHub API – E-Learning Platform Backend

EduHub is a backend API for managing courses, lessons, enrollments, and progress tracking with flexible pricing models and certificate generation.

---

## 🚀 Requirements

- PHP 8.2+
- Composer 2.x
- MySQL 8.x (or compatible)
- Node.js & NPM (for optional frontend assets)
- Redis (for caching & queues)
- Laravel 10+

---

## ⚙️ Installation

1. **Clone the repository**

   ```bash
   git clone https://github.com/yourusername/eduhub-api.git
   cd eduhub-api

2. **Install dependencies**

   ```bash
   composer install
   npm install && npm run build

3. **Copy and configure the environment file**

   ```bash
   cp .env.example .env
   ```

   Update .env with your local database and mail settings:
   ```ini
   APP_NAME=EduHub
   APP_URL=http://localhost:8000
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=eduhub
   DB_USERNAME=root
   DB_PASSWORD=
    
   CACHE_DRIVER=redis
   QUEUE_CONNECTION=redis
    
   MAIL_MAILER=smtp
   MAIL_HOST=smtp.mailtrap.io
   MAIL_PORT=2525
   MAIL_USERNAME=your_mailtrap_username
   MAIL_PASSWORD=your_mailtrap_password
   MAIL_ENCRYPTION=tls
   MAIL_FROM_ADDRESS="noreply@eduhub.test"
   MAIL_FROM_NAME="${APP_NAME}"
   ```

4. **Generate application key**

   ```bash
   php artisan key:generate
   ```

5. **Run migrations and seed the database**

   ```bash
   php artisan migrate --seed
   ```

6. **Create the storage link (for certificates and media)**

   ```bash
   php artisan storage:link
   ```

7. **Run the application**

   ```bash
   php artisan serve
   ```

