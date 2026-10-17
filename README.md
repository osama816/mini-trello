# 🗂️ Mini Trello — نظام إدارة المشاريع والمهام (Kanban System)

نظام إدارة مشاريع ومهام تفاعلي متكامل تم بناؤه باستخدام **Laravel 11** و **Vue 3** مع **Inertia.js** و **Docker**.

---

## 🚀 التقنيات المستخدمة (Tech Stack)

- **Backend:** Laravel 11, PHP 8.3
- **Architecture:** Service Layer Pattern, Form Requests, Policies, Events & Listeners
- **Frontend:** Vue 3 (Composition API `<script setup>`), Inertia.js, Tailwind CSS
- **Database:** MySQL 8.0, Eloquent ORM with Eager Loading (Zero N+1)
- **Containerization:** Docker & Docker Compose (Nginx + PHP-FPM + MySQL)
- **Testing:** PHPUnit / Pest Test Suite

---

## 🗄️ تصميم قاعدة البيانات (Database ERD)

```text
users
├── id, name, email, password, timestamps
└── [1:N] ──> projects
└── [1:N] ──> tasks (assigned_to)
└── [1:N] ──> comments

projects
├── id, user_id (FK cascadeOnDelete), name, description
├── deleted_at (SoftDeletes), timestamps
└── [1:N] ──> tasks

tasks
├── id, project_id (FK cascadeOnDelete), assigned_to (FK nullable -> users)
├── title, description, status (todo / doing / done)
├── due_date, deleted_at (SoftDeletes), timestamps
├── INDEX(status, project_id)
└── [N:M] ──> tags (عبر pivot: tag_task)
└── [1:N] ──> comments

tags
├── id, name (unique), color, timestamps
└── [N:M] ──> tasks

tag_task (Pivot Table)
├── id, task_id (FK cascade), tag_id (FK cascade)
└── UNIQUE(task_id, tag_id)

comments
├── id, task_id (FK cascade), user_id (FK cascade), body, timestamps
```

---

## 💻 التشغيل المحلي السريع (Local Development)

### 1. المتطلبات:
- PHP >= 8.3
- Composer
- Node.js & NPM
- MySQL

### 2. خطوات التشغيل:
```bash
# تثبيت الاعتماديات
composer install
npm install

# ضبط ملف البيئة
cp .env.example .env
php artisan key:generate

# تشغيل الـ Migrations وتوليد البيانات التجريبية
php artisan migrate:fresh --seed

# بناء ملفات الواجهة
npm run build
# أو في وضع التطوير:
npm run dev

# تشغيل السيرفر
php artisan serve
```

### 🔑 بيانات تسجيل الدخول التجريبية (Demo Account):
- **Email:** `test@example.com`
- **Password:** `password`

---

## 🐳 التشغيل بواسطة Docker

المشروع يحتوي على بيئة معزولة بالكامل عبر Docker تتكون من 3 حاويات:
1. `mini_trello_app`: PHP-FPM 8.3
2. `mini_trello_nginx`: Nginx Web Server يستقبل الطلبات على البورت `8080`
3. `mini_trello_mysql`: MySQL 8.0 مع Persistent Volume

### تشغيل المشروع بأمر واحد:
```bash
# 1. تشغيل الحاويات في الخلفية
docker compose up -d

# 2. بناء قاعدة البيانات والبيانات الوهمية داخل الحاوية
docker compose exec app php artisan migrate --seed

# 3. فتح التطبيق في المتصفح:
# http://localhost:8080
```

---

## 🧩 الهيكلية وأفضل الممارسات (Clean Architecture)

- **Service Layer Pattern:** تم عزل الـ Business Logic داخل `app/Services/ProjectService.php` و `app/Services/TaskService.php` لإبقاء الـ Controllers رشيقة ومحصورة فقط في استقبال الـ Requests وتوجيه الـ Responses.
- **Authorization & Security:** تطبيق `ProjectPolicy` لحماية البيانات، بحيث لا يمكن لأي مستخدم الوصول أو تعديل أو حذف أي مشروع لا يملكه.
- **Form Requests:** التحقق من صحة المدخلات في `StoreTaskRequest`, `UpdateTaskRequest`, `StoreProjectRequest`.
- **Events & Listeners:** إطلاق حدث `TaskAssigned` عند تعيين أو إعادة تعيين مهمة لشخص وتسجيلها عبر `LogTaskAssignment`.
- **Eager Loading:** استخدام `with(['assignedUser', 'tags', 'comments.user'])` لمنع مشكلة N+1 Query Problem نهائياً.
- **Frontend Kanban:** لوحة 3 أعمدة تفاعلية مع Drag & Drop فوري يحدّث الحالة بدون Reload عبر `router.patch` الخاص بـ Inertia.js.

---

## 🌿 شجرة الفروع (Git Branches):
```text
main           ← الـ Production النهائي (Tag: v1.0)
  └─ develop   ← تجميع ومزامنة المميزات
       ├─ feature/projects  (Database & Backend)
       ├─ feature/tasks     (Vue 3 & Kanban Board)
       └─ feature/docker    (Docker & Compose Setup)
```
