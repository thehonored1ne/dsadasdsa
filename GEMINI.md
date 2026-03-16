AGENT.md — Hackathon App: Automated Teacher-Subject Assignment System
Project Overview
A high-performance Laravel 12 + Breeze web application featuring a premium SaaS-style UI. The system automates the complex task of assigning subjects to teachers using NLP matching, ensuring 0 conflicts while providing a professional administrative suite for Program Chairs and a streamlined portal for Faculty.

Tech Stack
Backend: Laravel 12, PHP 8.2

Frontend: Tailwind CSS (Modern SaaS Preset), Blade, Alpine.js, Chart.js

Auth: Laravel Breeze (Customized UI)

Database: SQL (MySQL/MariaDB)

PDF Export: barryvdh/laravel-dompdf

Excel Support: phpoffice/phpspreadsheet

AI: TF-IDF NLP matching algorithm (Local Implementation)

Timezone: Asia/Manila

Project Structure
app/
├── Helpers/
│   └── FileImportHelper.php       # Handles CSV + Excel file parsing
├── Http/
│   └── Controllers/
│       ├── AssignmentController.php   # Generation engine + manual overrides
│       ├── ChairController.php        # Imports, Reports, Audit Logs, Requests
│       └── TeacherController.php      # Dashboard, Profile, Notifications
├── Models/
│   ├── Assignment.php
│   ├── AuditLog.php
│   ├── Subject.php
│   └── ... (All core entities)
└── Services/
    ├── AssignmentService.php      # Core Engine: TF-IDF + Conflict skip logic
    ├── TFIDFService.php           # NLP Tokenization & Similarity logic
    └── ...
UI Design Language
Typography: Inter / System Sans-Serif (Font weights: 900 Black, 700 Bold).

Geometry: 3xl rounded corners (rounded-3xl) for cards and containers.

Contrast: WCAG AA compliant slate-grays (#475569) and indigo-blacks.

Visuals: Dynamic progress bars for match scores and color-coded status pips.

Feature Modules
1. Auto-Assignment Engine (TF-IDF NLP)
Threshold: Match scores below 0.3 fallback to availability rationale.

Logic: Prerequisites Check → NLP Scoring → Availability Overlap → Overload Flagging.

Integrity: Enforces 0 scheduling conflicts by design.

2. Document Repository 🚧 IN PROGRESS
Planned: Centralized storage for institutional memos, syllabus templates, and forms.

Status: Backend migration and model logic staged; UI integration pending.

3. Conflict & Audit Tracking
Dashboard: "Scheduling Conflicts" card remains a verified Green 0.

Audit Log: Every manual override or engine run is logged with a "From -> To" trace.

Success States: Explicit reporting of "X Created, Y Skipped, 0 Conflicts."

4. Faculty Portal
Dashboard: Quick view of assigned subjects, total units, and load status.

Teaching Profile: Self-service management of expertise areas and time slots.

Notifications: Real-time alerts for schedule changes or chair responses.

Current Status
✅ Modern UI: Full SaaS-style redesign completed across all views.

✅ Accessibility: Darkened grays and high-contrast labels implemented.

✅ TF-IDF Engine: Fully functional with match score progress bars.

🚧 Document Repository: Development in progress (Migration staged).

✅ Error Handling: Row-level validation for CSV/Excel imports.

✅ Audit Trail: Newest-first logging in Asia/Manila timezone.

✅ 0 Conflicts: Engine verified and dashboard-enforced.