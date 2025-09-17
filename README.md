# 🛠 Project Setup Guide

This guide will help you set up the **Online Food Ordering Web App** locally, both for backend (Laravel) and frontend (React depending on implementation).

---

## 📌 Prerequisites

Before starting, make sure you have the following installed:

* **Git** → [Download](https://git-scm.com/)
* **PHP** (>= 8.2) → [Download](https://www.php.net/downloads)
* **Composer** → [Download](https://getcomposer.org/)
* **Node.js & npm** (LTS recommended) → [Download](https://nodejs.org/)
* **MySQL / MariaDB** (or any supported database)
* **Laravel Installer (optional)** → installed via Composer

---

## 🚀 Backend Setup (Laravel)

1. **Clone the repository:**

   ```bash
   git clone https://github.com/your-org/your-repo.git
   cd your-repo/backend
   ```

2. **Install dependencies:**

   ```bash
   composer install
   ```

3. **Create environment file:**

   ```bash
   cp .env.example .env
   ```

4. **Generate app key:**

   ```bash
   php artisan key:generate
   ```

5. **Configure database** in `.env` file:

   ```env
   DB_DATABASE=your_database
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

6. **Run migrations:**

   ```bash
   php artisan migrate
   ```

7. **Start the Laravel server:**

   ```bash
   php artisan serve
   ```

   > The backend should now run at `http://127.0.0.1:8000`

---

## 🎨 Frontend Setup (React)

1. **Go to frontend folder:**

   ```bash
   cd ../frontend
   ```

2. **Install dependencies:**

   ```bash
   npm install
   ```

3. **Start development server:**

   ```bash
   npm run dev
   ```

   > The frontend should now run at `http://localhost:5173` (or another port)

---

## 🔗 Connecting Frontend & Backend

* In the frontend `.env` file, set your backend API URL:

  ```env
  VITE_API_BASE_URL=http://127.0.0.1:8000
  ```

---

## ✅ Troubleshooting

* If `composer install` fails, make sure **PHP & Composer** are installed and available in PATH.
* If `npm install` fails, try deleting `node_modules` and `package-lock.json`, then run `npm install` again.
* For database issues, confirm MySQL is running and credentials in `.env` are correct.

---

Now you’re ready to start contributing 🎉.
