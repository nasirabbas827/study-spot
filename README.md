# study_spot_final  

A web‑based learning management system that allows administrators, teachers, and course creators to manage courses, lectures, PDFs, quizzes, and student progress. Built with PHP and MySQL, the application provides a clean admin interface and a responsive front‑end for learners.

---  

## Overview  

`study_spot_final` is a complete PHP application for creating and delivering online courses.  
Key capabilities include:

* Secure admin authentication  
* CRUD operations for courses, lectures, PDFs, quizzes, and teachers  
* Enrollment management and progress tracking for students  
* Separate roles for **Course Creators** and **Teachers**  
* Exportable data via MySQL dump (`Database/studyspot.sql`)  

The repository contains all source files, a database schema, and a brief project specification (`StudySpot Project.docx`).

---  

## Features  

| Category | Feature |
|----------|---------|
| **Administration** | Admin login, dashboard, navigation bar, logout |
| **Course Management** | Add / edit / view courses, assign course creators |
| **Content Management** | Add / edit / view lectures, PDFs, quizzes |
| **User Management** | Add / edit / view teachers, enroll / cancel enrollment, view student progress |
| **Reporting** | List of enrolled users, teacher overview, quiz results |
| **Security** | Session‑based authentication, prepared statements (via `config.php`) |
| **Database** | MySQL schema (`Database/studyspot.sql`) ready for import |

---  

## Tech Stack  

| Layer | Technology |
|-------|------------|
| **Backend** | PHP 7.4+ |
| **Database** | MySQL / MariaDB |
| **Web Server** | Apache (or any server supporting PHP) |
| **Front‑end** | HTML5, CSS3, Bootstrap (included in admin templates) |
| **Version Control** | Git (GitHub) |

---  

## Installation  

1. **Clone the repository**  

   ```bash
   git clone https://github.com/yourusername/study_spot_final.git
   cd study_spot_final
   ```

2. **Create a MySQL database**  

   ```sql
   CREATE DATABASE studyspot CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

3. **Import the schema**  

   ```bash
   mysql -u your_user -p studyspot < Database/studyspot.sql
   ```

4. **Configure the application**  

   - Copy `config.php.example` to `config.php` (or edit the existing `config.php`).  
   - Update the following constants with your environment values:

     ```php
     define('DB_HOST', 'YOUR_DB_HOST');
     define('DB_NAME', 'studyspot');
     define('DB_USER', 'YOUR_DB_USER');
     define('DB_PASS', 'YOUR_DB_PASSWORD');
     ```

5. **Set up the web server**  

   - Place the project folder inside your web root (e.g., `htdocs` or `public_html`).  
   - Ensure the `admin/` directory is reachable via `http://yourdomain.com/admin/`.  
   - Enable `mod_rewrite` if you plan to use pretty URLs.

6. **Adjust file permissions** (if needed)

   ```bash
   chmod -R 755 admin/
   ```

7. **Start the server** and navigate to the admin login page:

   ```
   http://localhost/study_spot_final/admin/admin_login.php
   ```

---  

## Usage  

### Admin  

1. **Login** with the default credentials (created during the DB import) or create a new admin via `admin/add_admin.php` (if you add such a script).  
2. Use the **Admin Dashboard** (`admin/admin_home.php`) to navigate to:
   * **Courses** – add, edit, view, or delete courses.
   * **Lectures** – manage lecture content.
   * **PDFs** – upload and associate PDFs