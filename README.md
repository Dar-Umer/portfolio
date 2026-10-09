# The Umer Store
## Personal Portfolio, Client Portal, Project Management, Advanced Ticketing, GitHub Integration & Billing System

Production-ready personal website and client management platform built using **Laravel 12**, **MySQL / MariaDB**, and **Spatie Laravel Permission**.

---

### Core Architecture & Highlights

- **Multi-Tenant Client Portal:** Strict data isolation preventing cross-tenant access.
- **Priority Ticket Management Engine:**
  - Automated SLA tracking and countdowns (Response & Resolution deadlines).
  - Configurable state machine workflow transitions.
  - Granular separation of public comments vs internal staff notes (`is_internal_note`).
  - GitHub link associations (commits, branches, PRs, and tags).
- **Billing & GST Engine:**
  - Concurrency-safe document numbering (`TKT-YYYY-XXXXXX`, `INV-YYYY-XXXXXX`, `QTN-YYYY-XXXXXX`, `RCT-YYYY-XXXXXX`).
  - Immutable client billing snapshot on invoice issuance.
  - State-aware GST calculation (CGST + SGST for intra-state, IGST for inter-state).
  - Idempotent payment recording and invoice balance reconciliation.
- **Dynamic CMS Website:**
  - Dynamic homepage sections (Hero, About, Services, Featured Portfolio, Testimonials, Inquiry).
  - Responsive Bootstrap 5 and clean Blade components.
- **Centralized Audit Logging:**
  - Tracks user actions, status changes, and IP metadata.

---

### Default Credentials (Seeded)

- **Super Admin:**
  - Email: `admin@theumer.store`
  - Password: `password123`
- **Developer:**
  - Email: `dev@theumer.store`
  - Password: `password123`
- **Customer / Client:**
  - Email: `client@acme.com`
  - Password: `password123`

---

### Installation & Local Setup

1. **Clone repository & Install dependencies:**
   ```bash
   composer install
   ```

2. **Configure environment:**
   Ensure `.env` contains your MySQL database connection:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=theumer_store
   DB_USERNAME=root
   DB_PASSWORD=
   ```

3. **Run migrations and seed default data:**
   ```bash
   php artisan migrate:fresh --seed
   ```

4. **Run Automated Test Suite:**
   ```bash
   php artisan test
   ```

5. **Start Development Server:**
   ```bash
   php artisan serve
   ```
   Open `http://localhost:8000` in your browser.
