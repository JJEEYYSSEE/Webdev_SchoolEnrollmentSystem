# School Enrollment System

A web-based Senior High School (Grade 11–12, strand-based) enrollment system built with Laravel 13.

---

## Tech Stack

| Layer | Tech | Version |
|---|---|---|
| Backend | Laravel | 13 |
| Language | PHP | ^8.3 |
| Database | MySQL | 8.x |
| Frontend | Blade Templates | — |
| CSS | Bootstrap | 5.3 |
| Icons | Bootstrap Icons | — |
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

## Overview

Two roles share one system:

- **Students** register, pick a section for the active semester (subjects are fixed per
  section and auto-loaded), and track their enrollment status, subjects, and records.
- **Registrars** open/close the enrollment period per semester, manage sections and
  subjects, review enrollments (approve / reject / batch approve), encode grades, and
  finalize semesters.

### User Flow

```
Landing (/) → choose role
  Student → register (strand + grade) → dashboard
          → enroll (pick a section for the active semester)
          → enrollment created (pending) + subjects snapshotted
          → view status / section / subjects / records
  Registrar → log in
          → create + activate a school year, set active semester, open enrollment
          → review enrollment queue → approve / reject (with feedback) / batch approve
          → encode grades → finalize semester (locks records + GPA)
```

Key rules:
- **Active semester gate** — enrollment only works when a school year is active, the
  active semester is set (1st / 2nd), and the registrar has opened enrollment.
- **No manual subject picking** — choosing a section enrolls the student in all of that
  section's subjects (snapshot copied to `enrollment_subjects`).
- **Section capacity** — a section that is full hard-blocks further approvals.
- **Rejection freezes the application** for that semester. The student must comply with the
  registrar's feedback; only the registrar can reopen it (revert to pending, with a reason),
  or the student applies again next semester.

---

## Requirements

- PHP 8.3+
- Composer
- MySQL 8.x
- Node.js 18+ and npm (only if changing frontend assets)

---

## Local Setup

> On Windows, use PowerShell.

```powershell
# 1. Clone the repo
git clone https://github.com/JJEEYYSSEE/Webdev_SchoolEnrollmentSystem.git
cd Webdev_SchoolEnrollmentSystem

# 2. Install PHP dependencies
composer install

# 3. Copy environment file
copy .env.example .env
php artisan key:generate

# 4. Create MySQL database
# CREATE DATABASE school_enrollment_db;

# 5. Set DB credentials in .env (DB_DATABASE, DB_USERNAME, DB_PASSWORD)

# 6. Run migrations + seed sample data
php artisan migrate:fresh --seed

# 7. Start the server
php artisan serve
```

App runs at: `http://localhost:8000`

> `public/build/` is committed — teammates do not need to run `npm run dev`.

---

## Sample Accounts

All seeded accounts use the password **`password`** and are pre-verified.

| Role | Email | Notes |
|---|---|---|
| Registrar | `registrar1@school.edu.ph` | Liza Fernandez |
| Registrar | `registrar2@school.edu.ph` | Mark Villanueva |
| Student | `juan.delacruz@student.edu.ph` | STEM 11 — approved |
| Student | `maria.santos@student.edu.ph` | STEM 11 — pending |
| Student | `pedro.reyes@student.edu.ph` | ABM 11 — approved |
| Student | `ana.garcia@student.edu.ph` | STEM 11 — rejected |
| Student | `jose.ramos@student.edu.ph` | ABM 11 — pending |

Active semester after seeding: **S.Y. 2026-2027 · 1st Semester**, enrollment open.

> New students who self-register land straight on the dashboard (email verification is
> disabled). To re-enable it, have `User` implement `MustVerifyEmail` and add the
> `verified` middleware to the student routes.

---

## Database Tables

| Table | Description |
|---|---|
| `users` | Auth accounts — role: student / registrar |
| `students` | Student profile (strand + grade) linked to a user |
| `registrars` | Registrar profile linked to a user |
| `school_years` | School years — one active, with an active semester (1st/2nd) and enrollment open/closed |
| `strands` | SHS strands — STEM, ABM, HUMSS, GAS, TVL |
| `subjects` | Master subject list |
| `sections` | Class section per strand / grade / semester / school year |
| `section_subjects` | Fixed subjects assigned to each section |
| `enrollments` | Student enrollment per section — pending / approved / rejected |
| `enrollment_subjects` | Snapshot of subjects per enrollment, with grade and status |
| `semester_records` | GPA per student per semester, locked on finalization |
| `audit_logs` | Trail of registrar actions |

---

## Project Structure

```
app/
  Http/Controllers/
    Auth/           — Breeze auth + registration
    Student/        — Dashboard, Enrollment, Subject, Record, Section
    Registrar/      — Dashboard, Enrollment, Student, Section, Subject,
                      Semester, Grade, SemesterRecord
  Http/Middleware/
    CheckRole.php   — role-based access control
  Models/           — Eloquent models with relationships

resources/views/
  landing.blade.php — role selection
  auth/             — login, register, password reset
  layouts/          — app, guest, student, registrar base layouts
  student/          — student portal pages
  registrar/        — registrar portal pages (incl. semester, grades)

database/
  migrations/       — domain tables + Laravel defaults
  seeders/          — SchoolYear, Strand, Subject, User, Section,
                      SectionSubject, Enrollment

routes/
  web.php           — student + registrar route groups
  auth.php          — Breeze auth routes
```

---

## Routes

| Prefix | Middleware | Description |
|---|---|---|
| `/` | — | Landing / role selection |
| `/login`, `/register` | guest | Auth + student registration |
| `/student/*` | auth, role:student | Student portal |
| `/registrar/*` | auth, role:registrar | Registrar portal |
| `/profile` | auth | Profile edit |

---

## Verify Setup

```powershell
# All routes registered
php artisan route:list

# Migrations ran clean
php artisan migrate:status

# Run tests (includes portal smoke tests)
php artisan test
```

---

## Sharing with Teammates (ngrok)

```powershell
# Terminal 1
php artisan serve

# Terminal 2
ngrok http 8000
```

Copy the `https://abc123.ngrok-free.app` URL and share. Add to `.env`:

```env
APP_URL=https://abc123.ngrok-free.app
SESSION_DOMAIN=.ngrok-free.app
```

> Free tier URL changes every restart. Re-share each session.
