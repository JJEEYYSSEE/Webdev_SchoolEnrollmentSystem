# Admission & Enrollment Concept — SHS Enrollment System

Conceptual blueprint for splitting the current "register = instant student" flow into a
two-phase model: **Admission (Application)** then **Enrollment (Registration)**. This is the
source of truth for the redesign. Field lists are aligned to DepEd's Basic Education
Enrollment Form / SF10 / Learner Information.

---

## 1. Core reframe — two phases, not one

The current system makes a registered user a student instantly and drops them on the
dashboard. The redesign splits this into two separate gates.

| Phase | What it is | When | Output |
|---|---|---|---|
| **1. Admission (Application)** | Prove eligibility — submit info + documents, registrar approves. | Once, at Grade 11 entry | **School ID** + bona fide student status |
| **2. Enrollment (Registration)** | Register into sections/subjects for a term. | Every semester / school year (G11 & G12) | Assigned section + class schedule |

> **Application = admission** (become a student). **Enrollment = registration** (join classes).
> Two different things. Application is the door; enrollment is what you do each term after.

---

## 2. Locked decisions

| # | Decision |
|---|---|
| Admin role | **= registrar** (no separate admin role) |
| G11 enrollment | application **and** a separate enrollment form, same as G12 |
| School ID format | reuse `2026-00001` style, issued **at admission** |
| Login identity | **email** while applicant → **School ID + default password** after admission |
| Bad documents | **Invalid → reupload** only (no hard "Denied" state) |
| Schedule | **auto-generated** from DepEd class hours |
| Document storage | local `storage/` disk only (for now) |

---

## 3. Person lifecycle (state machine)

```
GUEST
  │ register (name, birthday, email, password)
  ▼
REGISTERED ── email not verified
  │ verify email (2FA step)
  ▼
EMAIL-VERIFIED ── routed to Application Wizard, NOT dashboard
  │ submit application form + upload PDFs
  ▼
APPLICANT (pending) ── registrar reviews form + documents
  │
  ├─► INVALID (return-for-compliance, orange ⚠) ──► reupload correct file ──┐
  │                                                                          │
  └─► QUALIFIED ──► system issues School ID + DEFAULT password ──────────────┘
                    + emails "You are qualified to enroll"
        ▼
BONA FIDE STUDENT ── logs in with SCHOOL ID + default password
  │ first login → forced change password (existing OTP feature)
  │ submit ENROLLMENT (per term) + requirements
  ▼
ENROLLED ── registrar approves ──► assigned section + AUTO-GENERATED schedule
```

---

## 4. Auth model

**Login identity switches mid-lifecycle:**

- **Applicant** logs in with **email + password** (the ones set at registration) to fill and
  track the application. No School ID exists yet.
- Once registrar marks the application **Qualified**, the system generates a **School ID +
  default password**, emails them, and from then on login uses the **School ID**. Email
  remains only for notifications.
- First School-ID login forces a password change via the existing change-password (OTP) feature.

**Gating:** the `verified` middleware is no longer enough. A new **admitted / bona-fide gate**
bounces email-verified-but-not-yet-admitted users to the application/status page until a
School ID exists.

---

## 5. Initial registration (trimmed)

Registration only creates a login. Everything else moves into the application wizard.

| Field | Required |
|---|---|
| First name / Last name | ✔ |
| Birthday | ✔ |
| Email (verification target) | ✔ |
| Password + confirmation | ✔ |

> After email verification, **name + birthday auto-prefill** into the application wizard.
> Strand, address, LRN, parents, and documents are collected in the wizard, not here.

---

## 6. Application Wizard (Grade 11 admission)

Multi-step wizard with a progress bar, per-step validation, save-as-draft, and a final
review before submit (SSS-style).

```
[1] Personal Info → [2] Educational Background → [3] Documents → [4] Review → ✓ Submitted
```

### Step 1 — Personal Information
DepEd Learner Information. (★ = prefilled from the account)

| Field | Type | Req |
|---|---|---|
| LRN (Learner Reference No., 12-digit) | text | ✔ (or "No LRN" checkbox) |
| Last / First / Middle name ★ | text | ✔ (middle optional) |
| Extension (Jr., III) | text | — |
| Birthdate ★ | date | ✔ |
| Sex | select M/F | ✔ |
| Age | auto from birthdate | auto |
| Place of birth | text | ✔ |
| Civil status | select | — |
| Mother tongue | text | ✔ |
| Religion | text | — |
| Belongs to IP community | toggle + specify | — |
| Has disability / special needs | toggle + type | — |
| 4Ps beneficiary | toggle + Household ID | — |
| Mobile no. | text | ✔ |
| Email ★ | locked, from account | ✔ |
| Current address (house/street, barangay, city/municipality, province, zip) | fields | ✔ |
| Permanent address | "same as current" checkbox + fields | ✔ |

**Parent / Guardian:**

| Field | Req |
|---|---|
| Father — full name + contact | ✔ |
| Mother — maiden full name + contact | ✔ |
| Guardian — name, relationship, contact | ✔ (if not living with parents) |

### Step 2 — Educational Background
Typed values the registrar cross-checks against the uploaded SF10/SF9 for consistency.

| Field | Type | Req |
|---|---|---|
| Junior High School completed (name) | text | ✔ |
| JHS school ID / address | text | — |
| Year graduated (JHS / Grade 10) | year | ✔ |
| General average (Grade 10) | number | ✔ |
| Elementary school (name) | text | ✔ |
| Elementary year graduated | year | — |
| Returning learner (balik-aral)? | toggle | — |
| Transferee? | toggle + previous school | — |
| Track / Strand applying for ★ | select | ✔ |
| Grade level | fixed = 11 | auto |
| Semester / School year | from active SY | auto |

> Registrar UI shows these side-by-side with the uploaded documents → mismatch → mark Invalid.

### Step 3 — Upload Documents
DepEd SHS enrollment requirements. PDF/JPG/PNG, max ~5 MB each, local storage.

| Document | Req | Note |
|---|---|---|
| SF10 / Form 137 (permanent record) | ✔ | core admission proof |
| SF9 / Report Card (Grade 10, with general average) | ✔ | grades consistency |
| Certificate of Good Moral Character | ✔ | |
| PSA Birth Certificate | ✔ | identity / age |
| 2×2 ID photo | ✔ | used for the School ID later |
| JHS Completion Certificate | — | if separate |
| ESC / QVR Voucher certificate | — | private JHS only |
| Barangay clearance | — | optional |

Each row: file picker + preview + re-upload + status.

### Step 4 — Review & Submit
Read-only summary of steps 1–3 (collapsible, with per-section "Edit" jump-back), a
certification checkbox ("I certify the information is true"), then **Submit** → status
`pending` → redirect to the status tracker.

---

## 7. Registrar review (admission)

- List of applications filtered by status (pending / invalid / qualified).
- Open one → view the form + **each uploaded PDF inline**, side-by-side with the typed
  educational background for consistency checking.
- Decide:
  - **Qualified** → system issues School ID + default password, emails the applicant.
  - **Invalid** → return with a reason; applicant reuploads the correct file and resubmits.

---

## 8. Enrollment (per term, G11 & G12)

- **Grade 11:** already admitted via application; still submits a separate enrollment form
  (section confirmation) for consistency with Grade 12.
- **Grade 12:** separate enrollment form with its own requirements (e.g. Grade 11 report
  card / grades, clearance).
- Registrar reviews submitted files → approve / invalid (same return-for-compliance model).

### Terminology fix: "Reject" → "Invalid"

Not a hard denial — return-for-compliance ("something's wrong/missing, fix and resubmit").

| Status | Meaning | Color |
|---|---|---|
| Pending | awaiting review | gray / blue |
| **Invalid** (was Reject) | return, fix & resubmit | **orange / yellow ⚠** |
| Approved / Qualified | success | green ✓ |

---

## 9. Class schedule — auto-generation

Currently sections only carry an AM/PM tag and no real timetable. The generator builds a
weekly grid per section from DepEd class hours.

Rules:
- School day window: **7:30 AM – 5:00 PM**, Monday–Friday.
- AM section → fill morning slots first; PM section → afternoon.
- Slot length ~**60 min** (core subjects), lunch break 12:00–1:00 PM.
- Walk the section's `section_subjects`; drop each into the next free slot with **no teacher
  or room clash**.
- Assign room (and optionally teacher) → persist `day_of_week, start_time, end_time, room`
  per `section_subjects` row.
- Render a weekly grid (Mon–Fri × time) per section and per student.

---

## 10. Data model deltas (high level)

- `users`: trim registration fields; add `school_id` (null until admission, unique),
  `must_change_password` flag.
- `students`: add `school_id`, admission status; **move student-number generation from
  registration to the admission step**.
- new `applications`: applicant, grade level (11), status (pending / invalid / qualified),
  `reviewed_by`, remarks, draft progress.
- new `application_documents`: `application_id`, type (sf10 / sf9 / good_moral / psa /
  photo / …), file path.
- `enrollments`: rename status `rejected` → `invalid`; add `enrollment_documents`
  (`enrollment_id`, type, path).
- `section_subjects`: add `day_of_week`, `start_time`, `end_time`, `room`.
- Add **LRN** (12-digit) on applicant/student — national learner ID, separate from the
  internal School ID.

---

## 11. Post-application pending page

After submitting the application, the applicant lands here — not the dashboard. On any later
login (with email), an applicant whose status is not yet `qualified` is routed back to this
page. It is the applicant's home for the whole admission phase.

```
┌─────────────────────────────────────────────┐
│            ✓  Application Submitted           │
│                                               │
│   ⏳  Status: PENDING REVIEW                   │
│                                               │
│   ●━━━━━●━━━━━○                                │
│  Submitted  Under   Decision                  │
│             Review                             │
│                                               │
│   Ref No: APP-2026-00042                      │
│   Thank you! The registrar is reviewing your  │
│   application. We'll email you the result.     │
│                                               │
│   [ View my submission ]      [ Log out ]     │
└─────────────────────────────────────────────┘
```

Same page renders **3 states**:

| State | Look | Action |
|---|---|---|
| Pending | clock, "under review" | none — wait |
| Invalid (orange ⚠) | shows registrar's reason | **Re-upload & Resubmit** |
| Qualified (green ✓) | "Check your email for School ID" | log in next time with School ID |

---

## 12. Registrar decision, capacity & emails

Slot capacity forces **three** decision outcomes, not two. Each fires a different email.

| Decision | Trigger | Email | Result |
|---|---|---|---|
| **Qualified** | docs OK + slot free | A | School ID + default password issued |
| **No Slot / Waitlist** | docs OK but sections full | B | waitlisted, no School ID yet |
| **Invalid** | docs wrong / missing | C | return-for-compliance, reupload |

> **Recommended (pending final confirm):** no-slot = **waitlist + auto-notify** when a slot
> frees (not a hard "try next year"). Waitlisted applicants get a School ID **only once a slot
> is secured**, not while waiting.

The registrar dashboard reflects all of this (it is the source of the decisions):
- applications list with live status filters (pending / invalid / qualified / waitlisted) + counts
- **slot capacity panel** per strand + grade (e.g. STEM 11: 38/40) so they see when full
- acting on an application flips the applicant's status and fires the matching email

### Email A — Qualified
```
🎓 Congratulations! You are qualified to enroll at
   Cabrivex International Senior High School (CISHS).

   School ID:        2026-00043
   Default Password: Welcome@2026

Log in with your School ID and change your password.

— powered by E-Tala Enrollment System
```

### Email B — Slots full / waitlist
```
Your application to CISHS is qualified, but Grade 11 — STEM
slots are currently full. You have been WAITLISTED. We will
email your School ID the moment a slot opens.

— powered by E-Tala Enrollment System
```

### Email C — Invalid / return-for-compliance
```
Your CISHS application needs correction:
   <registrar's reason>

Please log in and re-upload the correct document, then resubmit.

— powered by E-Tala Enrollment System
```

---

## 13. Section assignment — auto, not registrar-picked

Manual section picking by the registrar risks favoritism/bias. Instead the **system
auto-assigns** at approval:

- match the applicant's **strand + grade**
- pick the section with the **most free slots** (load-balance), random tie-break
- if every matching section is full → trigger the **No Slot / Waitlist** path (Email B)

> **Recommended:** load-balance (even sections) over pure random. Registrar reviews
> *documents*; the system handles *placement*.

This single capacity model ties three features together: slot-free → auto-assign → Email A;
all full → waitlist → Email B; assigned section → feeds the auto-generated schedule (§9).

---

## 14. Branding (two-tier)

- **Primary brand (the school):** **Cabrivex International Senior High School (CISHS)** —
  shown as the main name on the auth card header, portal navbars (student/registrar),
  `<title>` tags, email headers, and the pending page.
- **Platform byline:** **"powered by E-Tala Enrollment System"** — small footer/subtext only.

`APP_NAME` in `.env` stays `E-Tala Enrollment System` (platform id, used for the mail
from-name). The displayed **school** name = CISHS, centralized in a config value
(e.g. `config('school.name')`) so it is one source of truth, not hardcoded per Blade view.

---

## 15. Open items to confirm later

- No-LRN applicants (some incoming G11 lack one) — registrar issues/links later?
- Exact Grade 12 enrollment requirement list.
- Teacher assignment in scope for the schedule generator, or rooms only for now?
- File upload caps and accepted MIME types (assumed 5 MB, PDF/JPG/PNG).
- No-slot semantics: waitlist + auto-notify (recommended) vs hard "next year".
- Section auto-assign: load-balance (recommended) vs pure random.
- Waitlisted applicants: School ID withheld until a slot opens (recommended).
