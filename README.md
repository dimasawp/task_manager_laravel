# 📋 Task Manager API (Laravel 12)

## 🚀 Deskripsi

Proyek ini adalah sebuah API Task Manager yang dibangun menggunakan Laravel 12.
API ini memungkinkan pengguna untuk melakukan operasi CRUD (Create, Read, Update, Delete) pada Projects, Tasks, dan Categories, lengkap dengan otentikasi pengguna, notifikasi, dan best practices Laravel modern.

## ✨ Features

-   🔑 Autentikasi Pengguna: Register, Login, Logout menggunakan Laravel Sanctum.
-   🗂️ Pengelolaan Projects:
    -   Membuat, melihat, memperbarui, dan menghapus project.
    -   Menampilkan project beserta daftar task-nya.
-   📝 Pengelolaan Tasks:
    -   CRUD task dengan kategori, project, prioritas, status, dan deadline.
    -   Subtask 1 level maksimum.
-   🏷️ Kategori (Category CRUD):
    -   Membuat, memperbarui, melihat, dan menghapus kategori.
-   📬 Notifikasi & Email:
    -   Notifikasi dikirim jika project/task memiliki deadline hari ini.
    -   Memanfaatkan Event, Listener, Job, Queue, Notification, dan Mail.
-   🔒 Akses Terproteksi: Semua operasi memerlukan otorisasi melalui Bearer Token.
-   ✅ Testing: Unit & Feature tests untuk memastikan fungsionalitas berjalan.

## 🛠 Tech Stack

-   Backend: Laravel 12 (PHP 8.2+)
-   Authentication: Laravel Sanctum
-   Database: PostgreSQL / MySQL / SQLite
-   Queue: Laravel Queue & Jobs
-   Testing: PHPUnit
-   Email: Laravel Mail

## ⚡ Quickstart

### 1️⃣ Requirements

-   PHP 8.2+
-   Composer
-   PostgreSQL / MySQL / SQLite
-   Docker (opsional, untuk environment konsisten)

### 2️⃣ Instalasi

```bash
# Kloning repositori
git clone https://github.com/dimasawp/task-manager-app.git
cd task-manager-app

# Instal dependensi
composer install

# Salin file environment
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 3️⃣ Database & Running

```bash
# Konfigurasi database di .env lalu jalankan:
php artisan migrate

# Jalankan server Laravel lokal
php artisan serve
```

### 4️⃣ Docker (Optional)

```bash
# Build dan jalankan container
docker-compose up -d --build

# Masuk ke container PHP
docker-compose exec app bash

# Di dalam container:
php artisan key:generate
php artisan migrate
php artisan serve
```

## 📡 API Endpoints

| Method | Endpoint             | Description            |
| ------ | -------------------- | ---------------------- |
| POST   | /api/register        | Register user          |
| POST   | /api/login           | Login user & get token |
| POST   | /api/logout          | Logout user            |
| GET    | /api/categories      | List all categories    |
| POST   | /api/categories      | Create category        |
| GET    | /api/categories/{id} | Get category detail    |
| PUT    | /api/categories/{id} | Update category        |
| DELETE | /api/categories/{id} | Delete category        |
| GET    | /api/projects        | List all projects      |
| POST   | /api/projects        | Create project         |
| GET    | /api/projects/{id}   | Get project detail     |
| PUT    | /api/projects/{id}   | Update project         |
| DELETE | /api/projects/{id}   | Delete project         |
| GET    | /api/tasks           | List all tasks         |
| POST   | /api/tasks           | Create task            |
| GET    | /api/tasks/{id}      | Get task detail        |
| PUT    | /api/tasks/{id}      | Update task            |
| DELETE | /api/tasks/{id}      | Delete task            |

### 💬 Response Format

```JSON
{
    "message": "Tasks retrieved successfully",
    "data": [
        // ... array of task objects
    ]
}
```

### 🧪 Testing

```bash
php artisan test
```

## Struktur Folder

Berikut adalah tree dari struktur folder utama proyek (disederhanakan):

```
🗂️ task-manager-app/
├── .github/
│   └── workflows/ci.yml
├── app/
│   ├── Console/
│   ├── Events/DeadlineCheckEvent.php
│   ├── Exceptions/
│   ├── Http/
│   │   ├── Controllers/Api/
│   │   │   ├── CategoryController.php
│   │   │   ├── ProjectController.php
│   │   │   └── TaskController.php
│   │   ├── Middleware/
│   │   └── Requests/
│   │       ├── StoreCategoryRequest.php
│   │       ├── UpdateCategoryRequest.php
│   │       ├── StoreProjectRequest.php
│   │       ├── UpdateProjectRequest.php
│   │       ├── StoreTaskRequest.php
│   │       └── UpdateTaskRequest.php
│   ├── Jobs/ProcessDeadlineNotificationJob.php
│   ├── Listeners/SendDeadlineNotificationListener.php
│   ├── Models/
│   │   ├── Category.php
│   │   ├── Project.php
│   │   └── Task.php
│   ├── Notifications/DeadlineTodayNotification.php
│   ├── Policies/
│   │   ├── CategoryPolicy.php
│   │   ├── ProjectPolicy.php
│   │   └── TaskPolicy.php
│   ├── Providers/AuthServiceProvider.php
│   └── Rules/OneLevelSubtask.php
├── database/
│   ├── factories/
│   ├── migrations/
│   └── seeders/
├── routes/api.php
└── tests/
    ├── Feature/
    └── Unit/
```

## 🏆 Best Practices

-   Controller tipis, logika bisnis di Service / Job /Listener.
-   Form Request khusus untuk validasi input.
-   Menggunakan Policies untuk otorisasi.
-   Event → Listener → Job → Notification untuk notifikasi deadline.
-   Testing lengkap dengan Unit & Feature Tests.
-   Response API konsisten dalam format JSON.

## ⚠️ Troubleshooting

-   401 Unauthorized
    -   Pastikan login terlebih dahulu untuk mendapatkan token.
    -   Sertakan header: Authorization: Bearer <token> saat request.
-   Queue Job Tidak Jalan
    -   Jalankan: php artisan queue:work atau gunakan supervisor untuk production.
-   Migrasi Gagal
    -   Pastikan database sudah dibuat dan .env dikonfigurasi dengan benar.

## 📝 Lisensi

MIT License © 2025
