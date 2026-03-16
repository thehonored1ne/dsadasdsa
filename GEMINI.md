# AGENT.md — Hackathon App: Automated Teacher-Subject Assignment System

## Project Overview
A Laravel 12 + Breeze web application that automates the assignment of subjects to teachers for a college/university setting. Built for a hackathon with two user roles: Program Chair and Teacher.

## Tech Stack
- **Backend**: Laravel 12, PHP 8.2
- **Frontend**: Blade templates, Tailwind CSS, Alpine.js, Chart.js
- **Auth**: Laravel Breeze
- **Database**: sqlite
- **PDF Export**: barryvdh/laravel-dompdf
- **Excel Support**: phpoffice/phpspreadsheet
- **AI**: TF-IDF NLP algorithm (local, no API needed) — ✅ IMPLEMENTED
- **Environment**: XAMPP on Windows

## Project Structure
```
app/
├── Helpers/
│   └── FileImportHelper.php       # Handles CSV + Excel file parsing
├── Http/
│   └── Controllers/
│       ├── Auth/
│       │   ├── AuthenticatedSessionController.php  # Role-based redirect after login
│       │   └── RegisteredUserController.php        # Auto-creates teacher profile on register
│       ├── AssignmentController.php   # Generate schedule + manual override
│       ├── ChairController.php        # All program chair features
│       └── TeacherController.php      # All teacher features
├── Models/
│   ├── Assignment.php
│   ├── AuditLog.php
│   ├── Availability.php
│   ├── Notification.php
│   ├── Schedule.php
│   ├── Subject.php
│   ├── TeacherProfile.php
│   ├── TeacherRequest.php
│   └── User.php
└── Services/
    ├── AssignmentService.php      # Core auto-assignment engine with TF-IDF
    ├── AuditLogService.php        # Logs all assignment actions
    ├── NotificationService.php    # Sends in-app notifications
    └── TFIDFService.php           # TF-IDF NLP matching algorithm ✅ IMPLEMENTED
```

## Database Tables
| Table | Purpose |
|---|---|
| `users` | Auth accounts with `role` column (program_chair / teacher) |
| `teacher_profiles` | Expertise areas, max units per teacher |
| `availabilities` | Teacher availability slots (day, time_start, time_end) |
| `subjects` | Subject catalog (code, name, units, prerequisites) |
| `schedules` | Available time slots (day, time_start, time_end, room) |
| `assignments` | Auto/manual assignments with rationale and `match_score` column |
| `audit_logs` | History of all generate and override actions |
| `notifications` | In-app notifications for teachers |
| `teacher_requests` | Teacher messages/requests to Program Chair |

## User Roles

### Program Chair
- Login → `/dashboard/chair`
- Upload CSV/Excel for teachers, subjects, schedules
- Download CSV and Excel templates for each upload type
- Auto-generate assignments via TF-IDF matching engine
- Manual override any assignment
- Search and filter assignments
- View match score per assignment
- View audit log
- View and respond to teacher requests
- Export Load Assignment Report to CSV and PDF

### Teacher
- Login → `/dashboard/teacher`
- View assigned subjects, schedule, units, load status
- Export own schedule to CSV
- Set expertise areas and availability (Teaching Profile page)
- Send requests to Program Chair (change subject, unavailability, load reduction, general message)
- Receive in-app notifications when assigned or overridden

## Routes Summary
```
// Program Chair
GET   /dashboard/chair
GET   /chair/upload
POST  /chair/upload/teachers
POST  /chair/upload/subjects
POST  /chair/upload/schedules
GET   /chair/templates/{type}/{format}   # Download CSV or Excel templates
GET   /chair/assignments
POST  /chair/assignments/generate
POST  /chair/assignments/override
GET   /chair/report
GET   /chair/report/export-csv
GET   /chair/report/export-pdf
GET   /chair/audit-log
GET   /chair/requests
PATCH /chair/requests/{id}/respond

// Teacher
GET   /dashboard/teacher
GET   /teacher/teaching-profile
PATCH /teacher/teaching-profile
GET   /teacher/export-schedule
GET   /teacher/notifications
PATCH /teacher/notifications/{id}/read
PATCH /teacher/notifications/read-all
GET   /teacher/requests
POST  /teacher/requests
```

## Key Business Logic

### Auto-Assignment Engine (AssignmentService)
Uses TF-IDF NLP scoring:
1. Clears previous auto-assignments (keeps manual overrides)
2. For each subject:
   - Checks prerequisites are already assigned
   - Scores all teachers using TFIDFService::calculateMatchScore()
   - Filters teachers with score >= 0.3 as expertise matches
   - Sorts by highest score first
   - Filters by availability (overlap check) and conflict detection
   - Assigns best scoring available teacher
   - Sets rationale: `expertise_match` (score >= 0.3) or `availability`
   - Stores match_score in assignment record
   - Flags overloaded teachers (total units > max_units)
   - Sends notification to assigned teacher
   - Logs to audit trail

### TF-IDF Matching (TFIDFService)
- Splits expertise areas by pipe `|`
- Tokenizes text: lowercase, remove punctuation, split by whitespace/dash
- Removes stopwords (and, or, the, a, an, introduction, basics, etc.)
- Compares subject tokens against expertise tokens:
  - Exact match → score 1.0
  - Prefix match (str_starts_with) → score 0.8
  - Levenshtein similarity >= 0.75 → partial score
- Returns highest score across all expertise areas (0.0 to 1.0)
- Threshold for expertise_match rationale: 0.3

### Availability Matching
- Uses **overlap check** (not strict cover check)
- Teacher available if: `time_start < schedule.time_end AND time_end > schedule.time_start`
- Allows flexible availability (e.g. 08:30-10:00 matches 08:00-09:00 slot)

### CSV/Excel Upload Error Handling
- Empty file detection
- Missing required column detection
- Per-row validation with row number in error messages
- try/catch wrapping for unexpected errors
- Partial success: valid rows imported, invalid rows reported
- Errors shown via `session('upload_error')` in upload.blade.php

### Downloadable Templates
- Program Chair can download pre-formatted templates for each upload type
- Available in both CSV and Excel (.xlsx) formats
- Templates include headers and one sample row
- Excel templates have blue styled header row with auto-sized columns
- Route: `GET /chair/templates/{type}/{format}` where type = teachers|subjects|schedules and format = csv|excel

### Account Creation — Two Ways
1. **CSV Upload** — Chair uploads teachers.csv, bulk creates accounts + profiles + availabilities
2. **Self Registration** — Teacher registers at /register, blank profile auto-created, teacher sets expertise/availability via Teaching Profile page

## CSV Formats

### teachers.csv
```
name,email,expertise_areas,max_units,available_days,time_start,time_end
John Cruz,john@school.edu,Programming|Web Development,21,Monday|Tuesday|Wednesday,08:00|08:00|08:00,09:00|09:00|09:00
```
- `expertise_areas`: pipe `|` separated
- `available_days`: pipe `|` separated, must match count of time_start and time_end
- `time_start`/`time_end`: pipe `|` separated, format HH:MM
- ⚠️ Do NOT use commas inside name or any field — it breaks CSV parsing

### subjects.csv
```
code,name,units,prerequisites
CS101,Introduction to Programming,3,
CS102,Web Development,3,CS101
```
- `prerequisites`: comma separated subject codes, or empty

### schedules.csv
```
day,time_start,time_end,room
Monday,08:00,09:00,Room 101
```
- `day`: Must be Monday-Sunday (exact spelling)
- Times: HH:MM format

## Seeder Credentials
| Role | Email | Password |
|---|---|---|
| Program Chair | chair@school.edu | password |
| Teacher | juan@school.edu | password |

## Views Structure
```
resources/views/
├── chair/
│   ├── assignments.blade.php      # Assignments table with search/filter/override/match score
│   ├── audit_log.blade.php        # Assignment history
│   ├── dashboard.blade.php        # Stats + Chart.js graphs + recent assignments
│   ├── report.blade.php           # Load assignment report + export buttons
│   ├── report_pdf.blade.php       # PDF template
│   ├── requests.blade.php         # Teacher requests inbox
│   └── upload.blade.php           # CSV/Excel upload forms + template download buttons
└── teacher/
    ├── dashboard.blade.php        # Assigned subjects + stats
    ├── notifications.blade.php    # In-app notifications
    ├── requests.blade.php         # Send requests to chair
    └── teaching_profile.blade.php # Set expertise + availability
```

## Known Behaviors
- Generating schedule clears all non-manual-override assignments first
- Teachers with no availability set will never be assigned
- Prerequisites must use exact subject codes from subjects table
- Expertise areas use pipe `|` as separator (not comma)
- TF-IDF threshold is 0.3 — scores below this labeled as `availability` rationale
- match_score stored as float in assignments table (null for manual overrides)
- Notifications sent on: generate schedule, manual override, request response
- Audit log tracks: schedule generation, manual overrides
- Registered teachers get blank profile — must set expertise/availability manually
- CSV upload creates teacher accounts with default password `teacher123`
- Excel template download uses coordinate strings (A1, B1) — NOT deprecated column/row integer methods
- PhpSpreadsheet v2+ API: use `setCellValue('A1', value)` and `getColumnDimension('A')` not column integer methods

## Dependencies
```json
"require": {
    "barryvdh/laravel-dompdf": "^3.1",
    "phpoffice/phpspreadsheet": "^5.5"
}
```

## Current Status
- ✅ Role-based auth (Program Chair + Teacher)
- ✅ CSV/Excel upload with error handling
- ✅ TF-IDF NLP matching engine
- ✅ Conflict detection
- ✅ Prerequisites enforcement
- ✅ Manual override
- ✅ Overload flagging
- ✅ Load Assignment Report (CSV + PDF export)
- ✅ Teacher load summary
- ✅ Dashboard with Chart.js graphs
- ✅ Search and filter assignments
- ✅ Match score column with progress bar in assignments table
- ✅ Audit log
- ✅ In-app notifications
- ✅ Teacher requests to Program Chair
- ✅ Teacher teaching profile (expertise + availability)
- ✅ Downloadable CSV and Excel templates for all upload types

## Typical Workflow
```
1. Chair downloads templates (CSV or Excel) from Upload page
2. Chair fills in teacher/subject/schedule data using templates
3. Chair uploads filled templates
4. Chair clicks Generate Schedule → TF-IDF engine matches teachers to subjects
5. Chair reviews assignments with match scores → overrides if needed
6. Chair exports Load Assignment Report (CSV or PDF)
7. Teachers log in → see assigned subjects + notifications
8. Teachers can update their teaching profile (expertise + availability)
9. Teachers can send requests to Chair if needed
```