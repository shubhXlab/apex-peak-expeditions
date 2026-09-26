# Apex Peak Expeditions v1.0.0 — Production Release & 1-Click Installer

Welcome to the official **v1.0.0 production release** of **Apex Peak Expeditions**, a high-altitude alpine expedition platform built on WordPress 6.7 with a custom relational database engine plugin, 10 fully designed responsive pages, high-definition alpine photography, and 1-click deployment automation.

---

## ⚡ Quickstart — 1-Click Automated Setup (Windows)

This release includes **`setup.bat`** inside the distribution archive `apex-peak-expeditions-v1.0.0.zip` for instant zero-configuration deployment:

1. Download **[`apex-peak-expeditions-v1.0.0.zip`](https://github.com/shubhXlab/apex-peak-expeditions/releases/download/v1.0.0/apex-peak-expeditions-v1.0.0.zip)** from the Assets section below.
2. Extract the archive.
3. Double-click **`setup.bat`** (or run from Command Prompt).

### What `setup.bat` Does Automatically:
- 🔍 **Environment Detection:** Scans for local XAMPP / MariaDB / MySQL installations and validates system PATH.
- 🚀 **Service Auto-Start:** Automatically launches MySQL and Apache background services if they are stopped.
- 🗄️ **Database Provisioning:** Creates the `wordpress` database (UTF-8 mb4) and imports `database/apex_peak_expeditions.sql` with all 10 pages, terms, and custom engine tables.
- 📂 **Target Webroot Deployment:** Deploys WordPress core files, plugins, themes, and photography assets directly into `C:\xampp\htdocs\wordpress`.
- ⚙️ **Config Auto-Generation:** Sets up production-ready `wp-config.php` with database credentials and secure authentication salts.
- 🌐 **Direct Launch:** Automatically opens `http://localhost/wordpress/` in your default web browser.

---

## 📦 What's Included in This Release

### 1. Custom Database Plugin: `Apex Expeditions Pro`
Located at `wp-content/plugins/apex-expeditions-pro/`:
- **4 Custom MySQL Tables:**
  - `wp_apex_expeditions`: Technical peak catalog, elevations, durations, difficulty tiers, pricing, and live slots.
  - `wp_apex_bookings`: Climber booking inquiries, contact numbers, seasons, and lead statuses.
  - `wp_apex_gear_inventory`: Equipment rental inventory and daily pricing.
  - `wp_apex_reviews`: Verified summit testimonials, ratings, and summit years.
- **REST API & AJAX Lead Desk:** Asynchronous booking form writing directly to MySQL (`POST /wp-json/apex/v1/book`).
- **Interactive Cost Calculator:** Live equipment rental estimator with real-time price calculations.
- **WP-Admin Management Suite:** 4 dedicated dashboard panels in WordPress administrator area.

### 2. Complete 10-Page Architecture
- **Home (`/`):** Mont Blanc ridge traverse hero banner, brand metrics, live route grid, reviews.
- **About Us (`/about-us/`):** Chamonix Valley HQ panorama, 15-year guiding legacy, IFMGA charter.
- **Expeditions (`/expeditions/`):** Ama Dablam pyramid spire, live route catalog pulled from `wp_apex_expeditions`.
- **Gear Guide (`/gear-guide/`):** Alpine gear room, hardgoods specifications, live DB rental calculator.
- **Safety & Medicine (`/safety-medicine/`):** High-altitude medical tent with Everest view, Gamow bags, SAR logistics.
- **Training (`/training/`):** Alpine trail conditioning, 24-week progressive framework benchmark tables.
- **Community Fund (`/community-fund/`):** Sherpa guides portrait at high camp, Solukhumbu educational endowment.
- **Summit Stories (`/summit-stories/`):** Sunrise summit celebration with ice axe, verified reviews from `wp_apex_reviews`.
- **Journal (`/journal/`):** Mont Blanc arête traverse, technical dispatches, and field notes.
- **Contact (`/contact/`):** Matterhorn Hörnli Ridge, 24/7 emergency dispatch, and live DB booking desk.

### 3. High-Definition Alpine Photography (Zero Duplication)
All 10 unique photography assets are uncompressed and included in `wp-content/uploads/2026/09/`.

### 4. Database Dump & Schemas
- `database/apex_peak_expeditions.sql`: Complete 2.4 MB MySQL snapshot (all pages, options, and tables).
- `database/schema.sql`: Clean standalone table definitions and seed data.

### 5. Docker Orchestration
Includes `docker-compose.yml` for multi-container deployment (WordPress 6.7 + MariaDB 10.11 + phpMyAdmin).

---

## 💾 Release Assets
- **`apex-peak-expeditions-v1.0.0.zip`:** Complete standalone distribution package including WordPress 6.7 core, custom plugin, theme, uploads, database dumps, and `setup.bat`.
- **Source code (zip & tar.gz):** Standard GitHub repository source archives.
