# FOWA Project

## 🛠 Installation Guide

### 1. Clone the repository
```bash
git clone https://github.com/Black-Reservoir/food-ordering-webapp.git
```

---

## ⚙️ Backend Setup (Laravel 12)
```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
```
- Update `.env` with your **database credentials**.
- Run migrations:
```bash
php artisan migrate
```
- Start server:
```bash
php artisan serve
```
➡️ Backend runs at: **http://localhost:8000**

---

## 🎨 Frontend Setup (React + Vite + Tailwind v4)
```bash
cd frontend
npm install
npm run dev
```
➡️ Frontend runs at: **http://localhost:5173**
