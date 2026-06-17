# School Enrollment System

A web-based school enrollment system built with Laravel 13.

---

## Tech Stack

| Layer | Tech | Version |
|---|---|---|
| Backend | Laravel | 13 |
| Language | PHP | ^8.3 |
| Database | MySQL | 8.x |
| Frontend | Blade Templates | — |
| CSS | Tailwind CSS | v4 |
| Build Tool | Vite | 8 |
| Auth | Laravel Breeze | — |
| Testing | PHPUnit | 12 |

---

## Group Members & Responsibilities

| Member | Role | Primary Deliverables |
|---|---|---|
| **Member A** | Project Lead / Back-end Core | Scaffolding, subject enrollment, middleware, deployment |
| **Member B** | Database & Back-end Support | ERD, migrations, seeders, section CRUD, admin dashboard |
| **Member C** | Auth & Flow Logic | Breeze auth, approval/rejection flow, UI polish |
| **Member D** | Front-end & Documentation | Enrollment form, student records view, README & submission docs |

---

## Pending Tasks by Member

### Member A — Project Lead / Back-end Core
- [x] Scaffold all controllers with stub methods and TODO comments
- [x] Define all routes (student + registrar groups) in `web.php`
- [x] Implement `CheckRole` middleware
- [x] Register `role` alias in `bootstrap/app.php`
- [ ] Define model relationships (hold until schema finalized)
- [ ] Set up deployment

### Member B — Database & Back-end Support
- [ ] Finalize and verify all migrations match ERD
- [ ] Run `php artisan migrate:fresh` to confirm clean migration
- [ ] Create seeders: `SemesterSeeder`, `UserSeeder`, `SectionSeeder`, `SubjectSeeder`
- [ ] Implement `Registrar/SectionController` CRUD methods
- [ ] Implement `Registrar/SubjectController` CRUD methods

### Member C — Auth & Flow Logic
- [ ] Implement `Registrar/EnrollmentController` — `approveEnrollment` and `rejectEnrollment`
- [ ] Implement `Student/EnrollmentController` — `postEnrollForm` and `showEnrollStatus`
- [ ] UI polish on login and register views
- [ ] (Optional) 2FA — install Fortify and wire challenge view

### Member D — Front-end & Documentation
- [ ] Build enrollment form UI (`resources/views/student/enroll.blade.php`)
- [ ] Build student records view (`resources/views/student/records.blade.php`)
- [ ] Build registrar enrollment queue (`resources/views/registrar/enrollments/index.blade.php`)
- [ ] Build registrar dashboard (`resources/views/registrar/dashboard.blade.php`)
- [ ] Update README with final submission docs

---

## Requirements

- PHP 8.3+
- Composer
- MySQL 8.x
- Node.js 18+ and npm

---

## Local Setup

> On Windows, use PowerShell. Commands prefixed with `#` are comments.

```powershell
# 1. Clone the repo
git clone https://github.com/JJEEYYSSEE/Webdev_SchoolEnrollmentSystem.git
cd Webdev_SchoolEnrollmentSystem

# 2. Install PHP dependencies
composer install

# 3. Install JS dependencies
npm install

# 4. Copy environment file
copy .env.example .env
php artisan key:generate

# 5. Create MySQL database (run in MySQL CLI or MySQL Workbench)
# CREATE DATABASE school_enrollment_db;

# 6. Open .env and set your DB credentials
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=school_enrollment_db
# DB_USERNAME=root
# DB_PASSWORD=yourpassword

# 7. Run migrations
php artisan migrate

# 8. Start servers — open TWO separate terminals
php artisan serve       # Terminal 1
npm run dev             # Terminal 2
```

App runs at: `http://localhost:8000`

---

## Verify Setup is Working

Run these after setup to confirm everything is wired correctly.

### Check all routes are registered
```powershell
php artisan route:list
```
Expected: you see login, register, student/*, registrar/* routes listed.

### Check only student routes
```powershell
php artisan route:list | Select-String "student"
```

### Check only registrar routes
```powershell
php artisan route:list | Select-String "registrar"
```

### Check middleware is registered
```powershell
php artisan route:list | Select-String "role"
```
Expected: student and registrar routes show `auth, role:student` or `auth, role:registrar` in middleware column.

### Check migrations run clean
```powershell
php artisan migrate:status
```
Expected: all migrations show `Ran` status. If any show `Pending`, run `php artisan migrate`.

### Wipe and redo all migrations (dev only — destroys all data)
```powershell
php artisan migrate:fresh
```

### Test auth flow manually
1. Go to `http://localhost:8000/login`
2. Register an account → should land on dashboard
3. Go to `http://localhost:8000/student/dashboard` while logged out → redirects to `/login` ✓
4. Log in as a user with `role = registrar`, visit `/student/dashboard` → gets `403 Forbidden` ✓
5. Log in as a user with `role = student`, visit `/student/dashboard` → passes through ✓

---

## What Was Scaffolded (Member A)

### Controllers (all stubs with TODO comments)
```
app/Http/Controllers/
  Auth/
    TwoFactorController.php       — showChallenge, postChallenge
  Student/
    DashboardController.php       — showDashboard
    EnrollmentController.php      — showEnrollForm, postEnrollForm, showEnrollStatus
    SubjectController.php         — showSubjects
    RecordController.php          — showRecords
  Registrar/
    DashboardController.php       — showDashboard
    EnrollmentController.php      — showEnrollments, showEnrollment, approveEnrollment, rejectEnrollment
    StudentController.php         — showStudents, showStudent
    SectionController.php         — showSections, showCreateSection, postCreateSection,
                                    showSection, showEditSection, updateSection, deleteSection
    SubjectController.php         — showSubjects, showCreateSubject, postCreateSubject,
                                    showSubject, showEditSubject, updateSubject, deleteSubject
    SemesterRecordController.php  — showSemesterRecord, updateSemesterRecord
```

### Routes
```
routes/
  web.php     — student group (prefix: student/, middleware: auth + role:student)
              — registrar group (prefix: registrar/, middleware: auth + role:registrar)
  auth.php    — Breeze: login, register, logout, password reset, email verify
```

### Middleware
```
app/Http/Middleware/CheckRole.php   — blocks access if role doesn't match
bootstrap/app.php                   — registers 'role' alias
```

### Migrations
```
database/migrations/
  create_users_table          — role enum(student, registrar, admin), 2FA columns
  create_semesters_table      — school_year, semester enum, is_active
  create_registrars_table     — user_id FK, first_name, last_name
  create_students_table       — user_id FK, student_number, name, phone, birthdate
  create_sections_table       — semester_id FK, section_name, year_level, slots
  create_subjects_table       — subject_code, subject_name, units
  create_enrollments_table    — student_id, semester_id, section_id, status enum, approved_by FK
  create_enrollment_subjects  — enrollment_id, subject_id, grade, status enum
  create_semester_records     — student_id, academic_year, semester, gpa, status enum
```

---

## Sharing with Teammates (ngrok)

ngrok creates a public URL tunneling to your local server.

### Install ngrok
1. Download from https://ngrok.com/download
2. Create free account at https://ngrok.com
3. Run: `ngrok config add-authtoken <your-token>`

### Share your local server
```powershell
# Terminal 1
php artisan serve

# Terminal 2
ngrok http 8000
```

Copy the `https://abc123.ngrok-free.app` URL and share with teammates.

### Add ngrok host to .env
```env
APP_URL=https://abc123.ngrok-free.app
SESSION_DOMAIN=.ngrok-free.app
```

> Free tier URL changes every restart. Re-share each session.

---

## Database Tables

| Table | Description |
|---|---|
| `users` | Auth accounts — role: student / registrar / admin |
| `students` | Student profile linked to user account |
| `registrars` | Registrar profile linked to user account |
| `semesters` | Academic semesters — one marked `is_active` at a time |
| `sections` | Class sections per semester |
| `subjects` | Master subject list |
| `enrollments` | Student enrollment per semester — pending / approved / rejected |
| `enrollment_subjects` | Subjects in an enrollment (pivot) with grade and status |
| `semester_records` | GPA and completion status per student per semester |

Schema SQL: `docs/school_enrollment_schema.sql`

---

## Project Structure

```
app/Http/Controllers/
  Auth/           — 2FA controller
  Student/        — student-facing controllers (stubs)
  Registrar/      — registrar-facing controllers (stubs)

app/Http/Middleware/
  CheckRole.php   — role-based access control

resources/views/
  auth/           — login, register, 2FA (Breeze)
  layouts/        — app, guest, student, registrar layouts
  student/        — student pages (stubs)
  registrar/      — registrar pages (stubs)
  components/     — reusable UI components (Breeze)

routes/
  web.php         — student + registrar route groups
  auth.php        — Breeze auth routes

database/migrations/
  — 9 domain tables + 3 Laravel defaults
```

---

## Running Tests

```powershell
php artisan test
```
