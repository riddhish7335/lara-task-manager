# Task Manager

A simple full-stack task management application built with **Laravel** (REST API) and **Vue 3** (SPA). Built as a portfolio project to demonstrate a clean, decoupled Laravel + Vue architecture with role-based access control.

## Overview

Two roles share the app:

- **Admin** — creates employee accounts, creates tasks, assigns tasks to employees, and can view every task in the system.
- **Employee** — sees only the tasks assigned to them and can mark a task as completed.

There is no public registration. Accounts are created only by an admin (or seeded), which keeps the demo self-contained.

## Tech Stack

**Backend**
- [Laravel 12](https://laravel.com) — API-only application
- [Laravel Sanctum](https://laravel.com/docs/sanctum) — token-based authentication (Bearer tokens, no cookies/CSRF)
- MySQL

**Frontend**
- [Vue 3](https://vuejs.org) (Composition API, `<script setup>`)
- [Vite](https://vitejs.dev) — dev server / build tool
- [Vue Router 4](https://router.vuejs.org) — client-side routing with auth/role guards
- [Axios](https://axios-http.com) — HTTP client with request/response interceptors
- [Bootstrap 5](https://getbootstrap.com) — styling

## Why token auth instead of cookie-based SPA auth?

The frontend and backend run as two separate applications on two different ports (`5173` and `8000`). Sanctum's cookie-based "SPA mode" is designed for same-site setups and requires careful configuration of stateful domains, session cookies, and CSRF — easy to misconfigure across ports/origins. Since this is a local/demo app, it instead uses **Sanctum personal access tokens**: login returns a bearer token, the SPA stores it and sends `Authorization: Bearer <token>` on every request. Simpler to reason about, and CORS only needs to allow the frontend origin (no credentials/cookies involved).

## Project Structure

```
lara-task-manager/
├── backend/              Laravel API
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/Api/   AuthController, EmployeeController, TaskController
│   │   │   └── Middleware/        EnsureUserHasRole (role:admin / role:employee)
│   │   └── Models/                User, Task
│   ├── database/
│   │   ├── migrations/
│   │   └── seeders/               DatabaseSeeder (creates the default admin)
│   └── routes/api.php
└── frontend/             Vue 3 SPA
    └── src/
        ├── api/axios.js           Axios instance + auth/401 interceptors
        ├── store/auth.js          Reactive auth state (user + token, persisted to localStorage)
        ├── router/index.js        Routes + auth/role navigation guards
        ├── views/
        │   ├── LoginView.vue
        │   ├── admin/DashboardView.vue     Employee list + create form
        │   ├── admin/TasksView.vue         All tasks + create/assign form
        │   └── employee/MyTasksView.vue    Own tasks + mark complete
        └── components/NavBar.vue
```

## Data Model

**users**
| column | notes |
|---|---|
| name, email, password | standard Laravel auth fields |
| role | `admin` \| `employee` (default `employee`) |

**tasks**
| column | notes |
|---|---|
| title, description | description is optional |
| due_date | optional date |
| status | `pending` \| `completed` (default `pending`) |
| assigned_to | FK → users.id (the employee the task is assigned to) |
| created_by | FK → users.id (the admin who created it) |

## API Reference

All endpoints are prefixed with `/api`. Endpoints under `auth:sanctum` require an `Authorization: Bearer <token>` header. Endpoints under `role:admin` additionally require the authenticated user to have the `admin` role.

| Method | Endpoint | Auth | Description |
|---|---|---|---|
| POST | `/login` | – | Log in with email/password, returns `{ user, token }` |
| POST | `/logout` | sanctum | Revokes the current token |
| GET | `/me` | sanctum | Returns the authenticated user |
| GET | `/tasks` | sanctum | Admin: all tasks. Employee: only their own tasks |
| PATCH | `/tasks/{task}` | sanctum | Admin: update any field. Employee: update `status` on their own task only |
| GET | `/employees` | admin | List all employees |
| POST | `/employees` | admin | Create a new employee account |
| POST | `/tasks` | admin | Create a task and assign it to an employee |

Authorization is enforced server-side in `TaskController` and `EmployeeController` — an employee token can never read another employee's tasks or hit admin-only endpoints (verified to return `403`).

## Getting Started

### Prerequisites

- PHP >= 8.2, Composer
- Node.js >= 18, npm
- MySQL (e.g. via XAMPP)

### 1. Backend setup

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
```

Create the database (defaults assume XAMPP's MySQL: user `root`, no password — edit `.env` if yours differs):

```sql
CREATE DATABASE lara_task_manager;
```

Run migrations and seed the default admin account:

```bash
php artisan migrate --seed
```

Start the API:

```bash
php artisan serve
```

API is now running at `http://localhost:8000`.

### 2. Frontend setup

```bash
cd frontend
npm install
npm run dev
```

App is now running at `http://localhost:5173`.

### 3. Log in

Use the seeded admin account:

```
email:    admin@example.com
password: password
```

## Usage Walkthrough

1. Log in as the seeded admin.
2. Go to **Employees** → create an employee account (name, email, password).
3. Go to **Tasks** → create a task, assign it to the new employee.
4. Log out, log back in with the employee's credentials.
5. On **My Tasks**, the employee sees only the task assigned to them and can click **Mark Complete**.
6. Log back in as admin — the task now shows status `completed` in the all-tasks table.

## Design Notes

This project intentionally stays minimal — it's meant to demonstrate a clean Laravel + Vue integration for a portfolio, not a production task-tracking tool. Deliberately out of scope: task comments/attachments, notifications, password reset, public registration, pagination, teams/projects. The UI uses plain Bootstrap components with no custom design system.

## License

MIT — feel free to use this as a reference or starting point.
