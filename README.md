# 🏔️ Apex Peak Expeditions

<div align="center">

[![WordPress](https://img.shields.io/badge/WordPress-6.7-21759B?logo=wordpress&logoColor=white)](https://wordpress.org/)
[![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?logo=php&logoColor=white)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-8.0%20%2F%20MariaDB-4479A1?logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Docker](https://img.shields.io/badge/Docker-Compose%20Ready-2496ED?logo=docker&logoColor=white)](https://www.docker.com/)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](LICENSE)
[![Maintenance](https://img.shields.io/badge/Maintained%3F-yes-green.svg)](https://github.com/shubhXlab/apex-peak-expeditions)

**A high-altitude mountaineering platform built on WordPress 6.7.**  
Featuring a custom MySQL database engine (`Apex Expeditions Pro`), dynamic lead management, interactive gear rental calculator, verified climber dispatch reviews, and a 10-page responsive editorial layout.

[Live Features](#-key-features) • [Page Directory](#-10-page-architecture) • [Custom Database Engine](#-custom-database-engine-apex-expeditions-pro) • [API Reference](#-rest-api-documentation) • [Installation](#-quickstart--installation) • [Contact](#-contact--maintainer)

</div>

---

## 📖 Overview

**Apex Peak Expeditions** is an alpine expedition platform. Built from the ground up to replace generic templates with a clean, high-performance architecture, the platform combines Gutenberg native block styling with a purpose-built database engine plugin (**Apex Expeditions Pro**).

Every page showcases high-definition photography with bottom-docked glassmorphic cards (`rgba(15, 23, 42, 0.88)`), ensuring mountain spires, summit ridges, and climbers remain visible.

---

## ⚡ Key Features

- **Custom MySQL Database Engine:** Standalone tables for routes, booking leads, gear inventory, and verified reviews (`dbDelta` orchestrated).
- **Interactive Lead Capture:** Asynchronous AJAX/REST API booking form writing directly into the relational database with auto-generated references.
- **Real-Time Cost Calculator:** Client-side equipment rental calculator pulling daily rates from the inventory database.
- **WP-Admin Management Suite:** 4 dedicated management panels in the WordPress administrator dashboard (Bookings, Route Catalog, Gear DB, and Engine Diagnostics).
- **Full Photo Layout System:** 950px height hero banners with anchor focal points (`50% 5%`) preserving 100% of photographic subjects across all screen viewports.
- **Zero Image Duplication:** 10 distinct high-resolution photography assets assigned across 10 pages.
- **Docker Compose Ready:** 1-command containerized stack with WordPress 6.7, MariaDB 10.11, and phpMyAdmin.

---

## 🗺️ 10-Page Architecture

| Page | Route | Description & Database Features |
| :--- | :--- | :--- |
| **Home** | `/` | Golden hour Mont Blanc ridge traverse, brand metrics, live route grid, reviews. |
| **About Us** | `/about-us/` | Chamonix Valley HQ panorama, 15-year guiding legacy, IFMGA charter. |
| **Expeditions** | `/expeditions/` | Ama Dablam pyramid spire, live route catalog pulled from `wp_apex_expeditions`. |
| **Gear Guide** | `/gear-guide/` | Alpine gear room, hardgoods specifications, live DB rental calculator. |
| **Safety & Medicine**| `/safety-medicine/`| High-altitude medical tent with Everest view, Gamow bags, SAR logistics. |
| **Training** | `/training/` | Alpine trail conditioning, 24-week progressive framework benchmark tables. |
| **Community Fund** | `/community-fund/`| Sherpa guides portrait at high camp, Solukhumbu educational endowment. |
| **Summit Stories** | `/summit-stories/`| Sunrise summit celebration with ice axe, verified reviews from `wp_apex_reviews`. |
| **Journal** | `/journal/` | Mont Blanc arête traverse, technical dispatches, and field notes. |
| **Contact** | `/contact/` | Matterhorn Hörnli Ridge, 24/7 emergency dispatch, and live DB booking desk. |

---

## 💾 Custom Database Engine (`Apex Expeditions Pro`)

The platform includes a custom plugin located in `wp-content/plugins/apex-expeditions-pro/` providing 4 dedicated MySQL tables:

```
┌───────────────────────────────────────────────────────────┐
│               Apex Expeditions Pro Plugin                 │
├─────────────────────────────┬─────────────────────────────┤
│   wp_apex_expeditions       │   wp_apex_bookings          │
│   - id (PK)                 │   - id (PK)                 │
│   - name, region            │   - expedition_name         │
│   - elevation_m, price_usd  │   - climber_name, email     │
│   - guide_ratio, slots      │   - phone, season, status   │
├─────────────────────────────┼─────────────────────────────┤
│   wp_apex_gear_inventory    │   wp_apex_reviews           │
│   - id (PK)                 │   - id (PK)                 │
│   - item_name, category     │   - climber_name, origin    │
│   - daily_price, stock      │   - route_name, rating      │
└─────────────────────────────┴─────────────────────────────┘
```

### 1. Database Tables Schema
- **`wp_apex_expeditions`:** Stores technical peaks, elevations, durations, difficulty tiers, pricing, and live remaining slots.
- **`wp_apex_bookings`:** Stores transactional booking inquiries, climber experience levels, contact numbers, and lead statuses.
- **`wp_apex_gear_inventory`:** Manages rental equipment inventory (crampons, technical axes, high-altitude boots) and daily rates.
- **`wp_apex_reviews`:** Stores verified climber field reviews, summit years, and star ratings.

### 2. Available Shortcodes
```php
[apex_expeditions_grid]   // Renders live expedition cards from the database
[apex_booking_form]        // Embeds AJAX booking reservation desk
[apex_gear_calculator]     // Interactive equipment cost estimator
[apex_summit_reviews]      // Verified climber testimonials grid
```

---

## 📡 REST API Documentation

The plugin registers custom REST API routes under the `apex/v1` namespace.

### `POST /wp-json/apex/v1/book`
Submit a climber departure reservation directly to the database.

**Request Payload:**
```json
{
  "climber_name": "Marcus Vance",
  "climber_email": "marcus.v@alps.ch",
  "climber_phone": "+41 79 123 4567",
  "expedition_name": "Matterhorn Hörnli Ridge",
  "departure_season": "Summer 2026",
  "experience_level": "Advanced",
  "notes": "Looking for 1:1 IFMGA guide pairing."
}
```

**Response (200 OK):**
```json
{
  "success": true,
  "booking_id": 5,
  "message": "Thank you! Your expedition request has been saved directly to our mountain database."
}
```

---

## 🚀 Quickstart & Installation

### Method 1: Docker Compose (Recommended)

1. **Clone the repository:**
   ```bash
   git clone https://github.com/shubhXlab/apex-peak-expeditions.git
   cd apex-peak-expeditions
   ```

2. **Configure environment:**
   ```bash
   cp .env.example .env
   ```

3. **Launch the stack:**
   ```bash
   docker compose up -d
   ```

4. **Access the environment:**
   - **Website:** `http://localhost:8080`
   - **phpMyAdmin:** `http://localhost:8081`

---

### Method 2: Manual Installation (XAMPP / LAMP / LocalWP)

1. Clone or copy files into your web root (e.g., `htdocs/wordpress`).
2. Create a MySQL database named `wordpress`.
3. Import the complete database snapshot:
   ```bash
   mysql -u root -p wordpress < database/apex_peak_expeditions.sql
   ```
4. Copy `wp-config.php.example` to `wp-config.php` and configure your database credentials.
5. Open your browser and navigate to `http://localhost/wordpress/`.

---

## 📁 Repository Structure

```
apex-peak-expeditions/
├── ci/
│   └── github-ci.yml.sample           # GitHub Actions CI workflow template (PHP linting)
├── database/
│   ├── apex_peak_expeditions.sql      # Full database snapshot (10 pages, options, tables)
│   └── schema.sql                     # Standalone DDL for custom tables & seeds
├── wp-content/
│   ├── plugins/
│   │   └── apex-expeditions-pro/      # Custom database engine plugin
│   ├── themes/
│   │   └── twentytwentyfive/          # Configured theme templates & styles
│   └── uploads/                       # 10 unique high-resolution mountain images
├── docker-compose.yml                 # Multi-container orchestration
├── wp-config.php.example              # Production-safe config template
├── .env.example                       # Environment configuration variables
├── .gitignore                         # WordPress standard exclusion rules
├── LICENSE                            # MIT License
└── README.md                          # Full documentation
```

---

## 👤 Contact & Maintainer

- **Maintainer:** Shubham Ramjiyani
- **Phone:** `+91 95032 48752`
- **Email:** `shubhamramjiyani2006@gmail.com`
- **GitHub:** [@shubhXlab](https://github.com/shubhXlab)

---

<div align="center">
  <sub>Built with precision for high-altitude explorers. © 2026 Apex Peak Expeditions.</sub>
</div>
