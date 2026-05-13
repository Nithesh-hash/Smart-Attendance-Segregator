# Smart Attendance Segregator

Built for VIT-IST — Office of Innovation, Startup and Technology Transfer, VIT Vellore

A full-stack web-based attendance processing platform that automates the segregation of mixed event attendance sheets into school-wise OD (On Duty) reports across 17 schools and 40+ department codes.

---

## 📌 Overview

At VIT, event attendance sheets often contain registration numbers from students belonging to multiple schools. Manually processing these sheets for OD approval across all schools was repetitive, time-consuming, and error-prone.

**Smart Attendance Segregator** streamlines this workflow by automating the complete attendance segregation process — from event registration to generation of school-wise Excel reports.

The system:

* Parses uploaded Excel attendance sheets
* Extracts and validates VIT registration numbers
* Maps department codes to corresponding schools
* Automatically segregates entries into school-specific datasets
* Generates formatted Excel reports for each school
* Bundles outputs into downloadable ZIP archives
* Maintains segregation history and analytics dashboards

The platform is actively deployed and used at VIT-IST, successfully processing **900+ OD entries** across major institutional events including:

* InnoAI 2026 AI/ML Hackathon
* Vinner’26 Hackathon
* Alumni Interaction Session on Innovations and Startups

---

# ✨ Features

## 🎯 Event Management

* Register and manage events with complete metadata
* Support for both single-day and multi-day events
* Venue and timing slot management
* Pending vs completed segregation tracking
* Admin controls for editing and deleting events/history

---

## ⚙️ Segregation Engine

* Automated segregation for **17 schools** and **40+ department codes**
* Excel parsing using PHPSpreadsheet
* Validation of VIT registration numbers
* Department-to-school mapping system
* Bulk multi-event segregation support
* School-wise formatted Excel reports
* ZIP-packaged downloadable outputs
* Auto-generated TXT and PDF summary reports
* Persistent segregation history stored in database

---

## 📊 Analytics Dashboard

* KPI cards for:

  * Events registered
  * Segregations completed
  * Students processed
  * Pending events

* Analytics visualisations including:

  * Monthly trends
  * Event type breakdown
  * School-wise participation
  * Venue utilisation
  * Faculty coordinator leaderboards

* Advanced filtering by:

  * Time range
  * Event type

* Downloadable analytics reports in PDF format

* Server-side JSON caching for performance optimisation

* AJAX-based lazy loading for efficient dashboard rendering

---

## 🔐 Security Features

* SQL Injection prevention using parameterised PDO queries
* Session regeneration after authentication
* Upload validation using `is_uploaded_file()`
* Environment variable management using `.env`
* Cache-control headers for authenticated pages
* Back-button hijack prevention using `history.pushState`

---

# 🛠️ Tech Stack

| Layer                  | Technology                     |
| ---------------------- | ------------------------------ |
| Backend                | PHP 8.1, PDO                   |
| Database               | MySQL 8                        |
| Frontend               | Vanilla JavaScript (ES6), AJAX |
| Excel Processing       | PHPSpreadsheet                 |
| Reports                | jsPDF, ZipArchive              |
| Visualisation          | Chart.js                       |
| Environment Management | vlucas/phpdotenv               |

---

# 🗄️ Database Schema

| Table                 | Description                                      |
| --------------------- | ------------------------------------------------ |
| `events`              | Stores event metadata and scheduling information |
| `segregation_history` | Maintains segregation run history                |
| `segregation_stats`   | Stores school-wise analytics data                |
| `schools`             | Department-code to school mapping                |

Complete schema available in `setup.sql`.

---

# 🚀 Installation

## Prerequisites

* PHP 8.1+
* MySQL 8+
* Composer
* Apache/Nginx with `.htaccess` support

---

## Setup Instructions

### 1. Clone the Repository

```bash
git clone https://github.com/yourusername/smart-attendance-segregator.git

cd smart-attendance-segregator
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Configure Environment Variables

```bash
cp _env .env
```

Update `.env` with your database credentials:

```env
DB_HOST=localhost
DB_NAME=attendance_segregator
DB_USER=your_db_user
DB_PASS=your_db_password
```

---

### 4. Set Up the Database

```bash
mysql -u root -p < setup.sql
```

---

### 5. Configure Web Server

Rename `_htaccess` to `.htaccess`:

```bash
mv _htaccess .htaccess
```

Create writable downloads directory:

```bash
mkdir -p downloads

chmod 777 downloads
```

---

### 6. Run the Application

```text
http://localhost/smart-attendance-segregator/
```

Default login credentials are configured inside `.env` using:

```env
APP_USER=
APP_PASS=
```

---

# 📁 Project Structure

```text
├── index.php
├── register_event.php
├── analytics_data.php
├── db.php
├── setup.sql
├── style.css
├── register_event.css
├── register_event.js
├── composer.json
├── _env
├── _htaccess
└── downloads/
```

---

# 🏫 Supported Schools & Department Codes

| School  | Department Codes                                 |
| ------- | ------------------------------------------------ |
| SENSE   | BVD, BEC, BML                                    |
| SCOPE   | BBS, BDS, BCT, BCB, MIC, BAI, MID, BCI, BKT, BCE |
| SCORE   | BIT, BCA, BCS, MCA, MAG, BYB, BDE, MIS           |
| SELECT  | BEE, BEL, BEI                                    |
| SMEC    | MMT, BMV, BST, BMA, BME, BMM                     |
| SBST    | BBT, MSI                                         |
| SSL     | BFN, BBC, BCC, BBP                               |
| VITBS   | BBA                                              |
| +9 More | Available in `setup.sql`                         |

---

# 👨‍💻 Team

| Role      | Name                |
| --------- | ------------------- |
| Developer | Nithesh Kumar T     |
| Developer | Umair Ahmed R       |
| Developer | Srishti Singh       |
| Mentor    | Dr. Jothish Kumar M |

---

# 📄 License

This project was developed for internal use at VIT-IST.
Please contact the authors before reuse or redistribution.

---

## ⭐ Acknowledgement

Built for VIT-IST, VIT Vellore to streamline institutional OD attendance processing through automation, analytics, and scalable event management.
