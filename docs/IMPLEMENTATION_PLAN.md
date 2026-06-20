# Implementation Plan — SHS Enrollment System

Based on the finalized user flow document. Use this as the source of truth for what to build.

---

## Open Decisions (Resolve Before Building)

| # | Question | Impact |
|---|---|---|
| 1 | **2FA** — both roles or students only? | `TwoFactorController`, auth flow, email config |
| 2 | **Rejection** — can students re-enroll, or is it final? | `enrollments` unique constraint, re-enrollment logic |
| 3 | **Section full** — hard block (reject at submit) or soft warning (show alert, still allow)? | `approveEnrollment` logic |
| 4 | **Grade encoding** — in scope for this submission? | If yes: `enrollment_subjects.grade`, `semester_records`, finalization |
| 5 | **Section assignment** — auto by system (match strand + grade) or manual by registrar? | Enrollment form UX and `postEnrollForm` logic |

---

## Schema Changes Still Needed

Run `php artisan migrate:fresh --seed` after each batch.

### `students` table — add strand + grade
```php
$table->foreignId('strand_id')->nullable()->constrained()->onDelete('set null');
$table->enum('grade_level', ['11', '12'])->nullable();
```
> Student sets these at registration. Used to auto-load subjects on enrollment form.

### `enrollment_subjects` table — restore grade tracking
```php
$table->decimal('grade', 3, 2)->nullable();
$table->enum('status', ['enrolled', 'passed', 'failed', 'dropped'])->default('enrolled');
```
> Only populate grade after semester ends (grade encoding). `status` locked on finalization.

### `semester_records` table — restore for GPA history
```php
$table->foreignId('student_id')->constrained()->onDelete('cascade');
$table->foreignId('school_year_id')->constrained()->onDelete('restrict');
$table->enum('semester', ['1st', '2nd']);
$table->decimal('gpa', 3, 2)->nullable();        // computed from enrollment_subjects.grade
$table->boolean('is_locked')->default(false);    // locked after finalization
$table->timestamps();
$table->unique(['student_id', 'school_year_id', 'semester']);
```

### `audit_logs` table — new, for registrar audit trail
```php
$table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
$table->string('action');           // e.g. 'approved_enrollment'
$table->string('model_type');       // e.g. 'Enrollment'
$table->unsignedBigInteger('model_id');
$table->json('old_values')->nullable();
$table->json('new_values')->nullable();
$table->timestamps();
```

---

## Routes To Add

```php
// Semester management (registrar)
Route::get('/semester', [RegistrarSemester::class, 'index'])->name('semester.index');
Route::post('/semester', [RegistrarSemester::class, 'store'])->name('semester.store');
Route::patch('/semester/{schoolYear}/toggle-enrollment', [RegistrarSemester::class, 'toggleEnrollment'])->name('semester.toggleEnrollment');

// Batch approve (registrar)
Route::post('/enrollments/batch-approve', [RegistrarEnrollment::class, 'batchApprove'])->name('batchApproveEnrollments');

// Grade encoding (registrar)
Route::get('/enrollments/{enrollment}/grades', [RegistrarGrade::class, 'show'])->name('showGradeForm');
Route::put('/enrollments/{enrollment}/grades', [RegistrarGrade::class, 'update'])->name('updateGrades');
Route::post('/semester/{schoolYear}/finalize', [RegistrarSemester::class, 'finalize'])->name('semester.finalize');

// Student section view
Route::get('/section', [StudentSection::class, 'show'])->name('showSection');
```

---

## Feature Build Order

### Phase 1 — Auth & Landing (Member C + A)

- [ ] **Landing page** (`/`) — role selection screen: "I'm a Student" → `/register`, "I'm a Registrar" → `/login`
- [ ] **Register** — student-only form: name, email, password + first_name, last_name, phone, birthdate, strand, grade_level
- [ ] **`RegisteredUserController`** — create `users` row (role=student) + `students` row in one transaction
- [ ] **Login** — existing Breeze flow is fine; role-based redirect already works
- [ ] **Email verification** — enable `MustVerifyEmail` on `User` model; block dashboard until verified
- [ ] **2FA** (pending group decision)
- [ ] Restore auth middleware on student + registrar routes in `web.php`

### Phase 2 — Semester Management (Member B)

- [ ] **`SemesterController`** (new controller, registrar group)
  - `index` — list all school years + active/open status
  - `store` — create new school year
  - `toggleEnrollment` — flip `is_enrollment_open` on a school_year
- [ ] View: semester management page with open/close toggle button
- [ ] Seeder: update `SchoolYearSeeder` with `is_enrollment_open`

### Phase 3 — Enrollment Flow (Member C)

- [ ] **`Student\EnrollmentController@showEnrollForm`**
  - Check `school_year.is_enrollment_open` → block with message if closed
  - Check student has no existing enrollment for this semester → block re-enrollment
  - Load student's section (from last enrollment or assigned by registrar)
  - Load subjects from `section_subjects` for that section
  - Render form showing pre-loaded subjects (read-only) + confirm button
- [ ] **`Student\EnrollmentController@postEnrollForm`**
  - Validate: active semester open, no duplicate enrollment
  - Create `enrollments` row (status=pending)
  - Copy `section_subjects` → `enrollment_subjects` (snapshot)
  - Redirect to status page
- [ ] **`Registrar\EnrollmentController@approveEnrollment`**
  - Update status=approved, set `approved_by`, `reviewed_at`
  - Check section capacity (`max_capacity` vs approved count) → block or warn
  - Write audit log entry
- [ ] **`Registrar\EnrollmentController@rejectEnrollment`**
  - Update status=rejected, save `remarks`
  - Write audit log entry
- [ ] **`Registrar\EnrollmentController@batchApprove`** — bulk update pending → approved
- [ ] **`Registrar\EnrollmentController@showEnrollments`** — add filter by track/year_level/section
- [ ] View: enrollment queue with working status tabs + track/section filter

### Phase 4 — Section & Subject Management (Member B)

- [ ] **`Registrar\SectionController`** — implement full CRUD
  - `showSections` — list grouped by grade_level → strand
  - `postCreateSection` — validate unique constraint
  - `updateSection`, `deleteSection` — guard: can't delete if enrollments reference it
  - Section form: add strand dropdown, grade_level, semester, time_period, max_capacity
  - Section detail: show assigned subject list + current enrolled count
- [ ] **`Registrar\SubjectController`** — implement full CRUD
  - Subject grouping by strand on index view
- [ ] **Section → Subject assignment UI** — on section detail page, allow adding/removing subjects from `section_subjects`
- [ ] View: sections index grouped by grade_level + strand (not flat table)

### Phase 5 — Student Management (Member B)

- [ ] **`Registrar\StudentController@showStudents`**
  - Grouped by grade_level → strand → section
  - Search by name / student_number / strand / grade_level
- [ ] **`Registrar\StudentController@showStudent`**
  - Profile + full enrollment history + semester records

### Phase 6 — Grade Encoding & Finalization (Member A — pending decision)

- [ ] **New `Registrar\GradeController`**
  - `show($enrollment)` — list subjects for enrollment with grade inputs
  - `update($enrollment)` — save grades per subject to `enrollment_subjects.grade`, set `status`
- [ ] **`Registrar\SemesterController@finalize`**
  - Compute GPA per student (average of subject grades)
  - Create/update `semester_records` row with GPA + `is_locked=true`
  - Lock `enrollment_subjects` (prevent further grade edits)
- [ ] Views: grade encoding form, semester finalization button

### Phase 7 — Student Records View (Member D)

- [ ] **`Student\RecordController@showRecords`** — list past `semester_records` with GPA
- [ ] **Student subjects view** — show current `enrollment_subjects` with grades (if encoded)
- [ ] **Student section view** — show assigned section for current semester

---

## What Already Exists (Don't Rebuild)

| What | Location | Status |
|---|---|---|
| All routes defined | `routes/web.php` | ✓ (middleware disabled for preview) |
| All controller stubs | `app/Http/Controllers/` | ✓ stubs + TEMP return view() |
| Bootstrap layouts | `resources/views/layouts/` | ✓ |
| All stub views with dummy data | `resources/views/student/`, `registrar/` | ✓ |
| Migrations (updated schema) | `database/migrations/` | ✓ |
| Seeders | `database/seeders/` | ✓ |
| CheckRole middleware | `app/Http/Middleware/CheckRole.php` | ✓ |

---

## Member Task Split (Suggested)

| Member | Phase | Deliverables |
|---|---|---|
| **A** | 1 (partial), 6 | Landing page, register view, route cleanup, grade encoding, deployment |
| **B** | 2, 4, 5 | Semester mgmt, section/subject CRUD, student grouping |
| **C** | 1 (auth logic), 3 | RegisteredUserController, full enrollment flow, approve/reject logic |
| **D** | 7 | Student records view, subject grades view, UI polish |
