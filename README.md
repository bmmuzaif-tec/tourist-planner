# 🌴 EravurGo – Local Tourist Day Visit Planner

EravurGo is a web-based tourist discovery and one-day visit planning system focused on Eravur and nearby destinations in the Batticaloa District of Sri Lanka.

The system helps visitors explore tourist places, view destination information and map locations, prepare a visit itinerary, and export a visit plan as a PDF.

> **Project:** ITE2953 – Programming Group Project (25S2)  
> **University:** University of Moratuwa  
> **Student:** Muzaif BM  
> **Registration No.:** E2410945  
> **Version:** 1.0

---

## 📌 Project Objectives

- Make information about local tourist destinations easier to discover.
- Allow visitors to browse places and filter them by category.
- Display destination information, location coordinates and map directions.
- Help visitors create a one-day itinerary.
- Allow visit plans to be exported as PDF documents.
- Provide visitor reviews and a contact-message facility.
- Provide an administration area for managing tourism information.

## ✨ Main Features

### Visitor features
- Browse and search tourist places.
- Filter destinations by category.
- View place descriptions, addresses, opening hours, travel tips and distances.
- View destination locations using an embedded Google Map.
- Open Google Maps directions to a selected destination.
- Create and view one-day visit plans.
- View an itinerary and estimated total distance based on the stored distance values of selected places.
- Export a visit plan as a PDF.
- Submit ratings and reviews.
- Send messages through the contact form.

### Administrator features
- Administrator login.
- Admin dashboard.
- Manage tourist places and categories.
- Manage visitor reviews and visit plans.
- View contact messages.

*The available administration actions depend on the pages and permissions implemented in the repository.*

## 🧰 Technologies Used

| Area | Technology |
|---|---|
| Frontend | HTML5, CSS3, JavaScript |
| UI framework | Bootstrap 5.3.3 |
| Icons | Bootstrap Icons |
| Backend | PHP |
| Database | MySQL |
| Database access | PHP MySQLi |
| PDF generation | FPDF |
| Map display | Google Maps embedded iframe |
| Directions | Google Maps Directions URL |
| Local development | WAMP Server |
| Code editor | Visual Studio Code |
| Version control | Git and GitHub |

### Map integration

The place details page reads the latitude and longitude stored for a destination in the MySQL database. These coordinates are used in a Google Maps embedded iframe to show the location.

The **Get Directions** link opens Google Maps with the destination coordinates.

This implementation uses an embedded map and a Google Maps URL; it is not the Google Maps JavaScript API integration.

## 🏗️ Application Architecture

The application follows a simple PHP web application structure:

1. **Presentation layer:** HTML, CSS, Bootstrap and JavaScript display the pages and handle browser-side interactions.
2. **Application layer:** PHP processes requests, retrieves data and performs application operations.
3. **Data layer:** MySQL stores tourist places, categories, administrators, visit plans, reviews and contact messages.

```text
Visitor / Administrator
          |
          v
   Web browser
 HTML + CSS + Bootstrap
       JavaScript
          |
          v
      PHP pages
          |
          v
    PHP MySQLi
          |
          v
     MySQL database
```

## 🗃️ Database Design

The SQL setup file is:

`database/database.sql`

It creates the `tourist_planner` database and the following seven tables:

| Table | Purpose |
|---|---|
| `categories` | Stores tourist-place categories. |
| `tourist_places` | Stores destination details and coordinates. |
| `administrator` | Stores administrator account information. |
| `visit_plan` | Stores visitor names and visit dates. |
| `visit_plan_details` | Connects visit plans with selected tourist places and visit order. |
| `reviews` | Stores visitor ratings and comments for places. |
| `contact_messages` | Stores messages submitted through the contact form. |

### Main relationships

- One category can be associated with many tourist places.
- One visit plan can contain multiple visit-plan details.
- One tourist place can appear in multiple visit-plan details.
- One tourist place can have multiple reviews.

`visit_plan_details` connects `visit_plan` and `tourist_places`.

## 📁 Repository Structure

The main files and directories include:

```text
tourist-planner/
├── index.php
├── places.php
├── place-details.php
├── planner.php
├── export-plan.php
├── about.php
├── contact.php
├── admin/
│   ├── login.php
│   └── ... administration pages
├── config/
│   ├── db.example.php
│   └── db.php                 # Local configuration; do not commit
├── includes/
│   ├── header.php
│   ├── navbar.php
│   └── footer.php
├── assets/
│   ├── css/
│   ├── js/
│   └── images/
├── database/
│   ├── database.sql
│   └── er.png
└── fpdf/                      # PDF library, if included in the checkout
```

The tree above highlights the main application files; some directories contain additional files and subdirectories.

## ⚙️ Installation – Windows 11 with WAMP

### 1. Install the development environment

Install and start WAMP Server. Make sure Apache and MySQL are running.

### 2. Clone the repository

Open Command Prompt or PowerShell:

```bash
cd C:\wamp64\www
git clone https://github.com/bmmuzaif-tec/tourist-planner.git
```

If you have already downloaded the repository, place the project folder inside:

`C:\wamp64\www\tourist-planner`

### 3. Create the database

1. Open phpMyAdmin: `http://localhost/phpmyadmin/`
2. Select **Import**.
3. Choose `database/database.sql` from the project.
4. Run the import.

The SQL file creates the `tourist_planner` database and its seven tables.

### 4. Configure the database connection

1. Open the project folder's `config` directory.
2. Copy `db.example.php`.
3. Rename the copy to `db.php`.
4. Update the connection values to match your local MySQL setup.

Example:

```php
<?php
$conn = mysqli_connect(
    "localhost",
    "root",
    "",
    "tourist_planner"
);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>
```

Use the username and password configured on your own computer. Do not commit real database credentials to GitHub.

### 5. Check the administrator table schema

**Important:** The current `database/database.sql` defines `administrator` with `admin_id`, `username` and `password`. However, the current `admin/login.php` also reads `full_name`, `role`, `failed_attempts` and `lock_until`.

If those four columns are not already present in your local database, run this once in phpMyAdmin:

```sql
ALTER TABLE administrator
    ADD COLUMN full_name VARCHAR(100) NULL,
    ADD COLUMN role VARCHAR(50) NOT NULL DEFAULT 'admin',
    ADD COLUMN failed_attempts INT NOT NULL DEFAULT 0,
    ADD COLUMN lock_until DATETIME NULL;
```

Do not run this alteration again if the columns already exist. Create an administrator account using a securely generated PHP `password_hash()` value; the login code checks passwords with `password_verify()`. Do not publish administrator passwords or private credentials in this repository.

### 6. Run the application

Open your browser and visit:

`http://localhost/tourist-planner/`

Administrator login page:

`http://localhost/tourist-planner/admin/login.php`

Use an administrator account that you have configured in your local database.

## 🧪 Suggested Testing Checklist

- [ ] Homepage loads successfully.
- [ ] Tourist places can be browsed.
- [ ] Search and category filtering work.
- [ ] A place details page displays the correct information.
- [ ] The embedded map displays the destination.
- [ ] The Get Directions link opens Google Maps.
- [ ] A visit plan can be created with selected places.
- [ ] The itinerary and estimated distance are displayed.
- [ ] A visit plan can be exported as a PDF.
- [ ] A visitor review can be submitted.
- [ ] The contact form can be submitted.
- [ ] Administrator login and available admin functions work.

## 🔐 Security and Configuration Notes

- The administrator login code uses a prepared statement for its username lookup.
- The login code uses `password_verify()` for password verification.
- The login code includes CSRF-token validation and session ID regeneration after successful login.
- The login code implements failed-login tracking and a temporary lockout.
- These protections in the login page do not mean every database operation across the whole application uses prepared statements. Review and validate all inputs and queries before deploying the project publicly.
- Keep `config/db.php` and real credentials out of version control.

## 🚧 Possible Future Improvements

- Add stronger server-side validation and prepared statements to all database operations.
- Improve authentication, authorization and administrator role management.
- Add automated tests for important visitor and administrator workflows.
- Improve itinerary distance calculation using actual road-route distances.
- Add accessibility and responsive-design refinements.
- Add deployment documentation and production environment configuration.

## 👨‍💻 Author

**Muzaif BM**  
Registration No.: E2410945  
University of Moratuwa  
Module: ITE2953 – Programming Group Project (25S2)

## 📄 License

This project was developed for academic purposes as part of the ITE2953 Programming Group Project. No separate open-source license is specified in this repository.
