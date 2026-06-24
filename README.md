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

Branded for **Cabrivex International Senior High School (CISHS)** — *powered by E-Tala Enrollment System*.

Admission and enrollment are two separate phases:

- **Applicants → Students** register, verify their email, then complete a DepEd-style Grade 11
  application (personal info, education background, document uploads). Once the registrar
  **qualifies** them they receive a **School ID + default password** and become bona fide
  students who enroll per semester and track their status, section, schedule, subjects, and records.
- **Registrars** review applications (qualify / waitlist / return), manage school years,
  sections and subjects, review enrollments, auto-generate class schedules, encode grades
  (100-point), and finalize semesters.

### Admission → Enrollment flow

```
Landing (/) → choose role

Applicant (new)
  register (name, birthday, email, password) → verify email
  → application wizard: Personal → Education → Documents → Review → submit (pending)
  → registrar reviews:
       Qualified  → School ID + default password emailed
       Waitlisted → no remaining slot for that strand
       Invalid    → returned for compliance (fix + resubmit)
  → log in with School ID → forced password change → dashboard

Student (admitted)
  enroll for the active semester (Grade 12 uploads SF9 + 2x2 photo)
  → enrollment pending → registrar approves → assigned section + auto-generated schedule
  → view status / section / schedule / subjects / records

Registrar
  log in with Staff ID → review applications → manage semesters / sections / subjects
  → review enrollments (approve / return as invalid) → generate schedules
  → encode grades (60–100) → finalize semester (locks records)
```

Key rules:
- **Login by School ID** — admitted students use their School ID, registrars their Staff ID
  (email also works). Applicants (no School ID yet) sign in with email to track or fix their
  application; the admission gate keeps them out of the dashboard until qualified.
- **Capacity + waitlist** — qualifying needs a free slot in the strand's sections; if full,
  the applicant is waitlisted instead of admitted.
- **Invalid = return-for-compliance** — a returned application/enrollment is *not* frozen; the
  student fixes the issue and re-submits (shown in orange).
- **No manual subject picking** — choosing a section enrolls the student in all its subjects
  (snapshot copied to `enrollment_subjects`).
- **Grades are 100-point** — 60 lowest, 75 passing, 90+ high.

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

All seeded accounts use the password **`password`**. **Log in with the School ID / Staff ID**
(email also works).

| Role | Login ID | Notes |
|---|---|---|
| Registrar | `REG-0001` | Liza Fernandez |
| Registrar | `REG-0002` | Mark Villanueva |
| Grade 12 student | `2025-00001` … `2025-00132` | admitted, varied strands + section fills, with grades + past records |
| Grade 12 — enroll test | `2025-12900` (STEM), `2025-12901` (ABM) | **not yet enrolled** — use to test the Grade 12 enrollment form (SF9 + 2x2 photo) |
| Grade 11 student | `2026-00001` … `2026-00005` | e.g. `2026-00004` was returned (invalid) — good for the resubmit flow |
| Grade 11 — enroll test | `2026-11900` | **not yet enrolled** — Grade 11 enrollment form (no documents) |

> **To test the registration / admission flow, create your own account** at `/register`
> (register → verify email → fill the application wizard). The seeded students above are
> already admitted, so they can't go through registration again.

> School IDs use the admission-year prefix: Grade 12 (admitted last year) = `2025-`,
> incoming Grade 11 = `2026-`.

Active semester after seeding: **S.Y. 2026-2027 · 1st Semester**, enrollment open.

> **Emails** need SMTP + network access. For local dev, set `MAIL_MAILER=log` in `.env` and
> read the verification link / School ID / OTP from `storage/logs/laravel.log`.

---

## Database Tables

| Table | Description |
|---|---|
| `users` | Auth accounts — role, School/Staff ID, must-change-password flag |
| `students` | Student profile (strand + grade), created at admission |
| `registrars` | Registrar profile linked to a user |
| `applications` | Grade 11 admission application (DepEd fields) — draft / pending / invalid / qualified / waitlisted |
| `application_documents` | Uploaded admission docs (SF10, SF9, good moral, PSA, 2x2) |
| `school_years` | School years — one active, with active semester (1st/2nd) and enrollment open/closed |
| `strands` | SHS strands — STEM, ABM, HUMSS, GAS, TVL |
| `subjects` | Master subject list |
| `sections` | Class section per strand / grade / semester / school year |
| `section_subjects` | Fixed subjects per section + the generated weekly schedule slot (day/time/room) |
| `enrollments` | Student enrollment per section — pending / approved / invalid (returned) |
| `enrollment_subjects` | Snapshot of subjects per enrollment, with grade and status |
| `enrollment_documents` | Grade 12 enrollment requirements (SF9, 2x2 photo) |
| `semester_records` | 100-point average per student per semester, locked on finalization |
| `audit_logs` | Trail of registrar actions |

---

## Project Structure

```
app/
  Http/Controllers/
    Auth/           — login (School ID/email), registration, OTP + first-password
    Student/        — Application (wizard), Dashboard, Enrollment, Subject, Record, Section
    Registrar/      — Dashboard, Application, Enrollment, Student, Section, Subject,
                      Semester, Grade, SemesterRecord
  Http/Middleware/
    CheckRole.php          — role-based access
    EnsureAdmitted.php     — keep un-admitted students in the application flow
    EnsurePasswordChanged.php — force first-login password change
  Services/
    ScheduleGenerator.php  — weekly timetable from DepEd class hours
  Notifications/    — qualified / waitlisted / returned / password OTP emails
  Models/           — Eloquent models with relationships

resources/views/
  landing.blade.php — role selection
  auth/             — login, register, first-password, verify
  application/      — admission wizard + status (applicants)
  emails/           — branded email bodies
  vendor/mail/      — CISHS Markdown mail theme
  layouts/          — app, guest, applicant, student, registrar base layouts
  student/          — student portal pages
  registrar/        — registrar portal pages (incl. applications, semester, grades, schedule)

database/
  migrations/       — domain tables + Laravel defaults
  seeders/          — SchoolYear, Strand, Subject, User, Section, SectionSubject,
                      Enrollment, Grade12Scenario, Grade

routes/
  web.php           — application + student + registrar route groups
  auth.php          — auth routes (login, OTP, verification)
```

---

## Routes

Login accepts a **School ID / Staff ID or email**.

| Prefix | Middleware | Description |
|---|---|---|
| `/` | — | Landing / role selection |
| `/login`, `/register` | guest | Login (School ID/email) + applicant registration |
| `/application*` | auth, verified, role:student | Admission wizard + status (applicants) |
| `/student/*` | auth, verified, admitted, role:student | Student portal (admitted only) |
| `/registrar/*` | auth, verified, role:registrar | Registrar portal (incl. applications, schedules) |
| `/first-password` | auth | Forced password change after first login |
| `/profile` | auth | Profile edit + password change (OTP) |

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
