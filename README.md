 Universal Systems: University Society & Club Management system

Welcome to Universal society and club management  System, a professional, enterprise-grade web application designed to streamline the management of university societies, events, financial records, and student engagement. Built with a modular architecture and a focus on scalability, this platform empowers university administrators and student leaders to foster a vibrant campus life.



 Features at a Glance
 🔐 Advanced Access Control
- Role-Based Access Control (RBAC):** Distinct dashboards and permissions for Super Admins, Society Heads, Event Managers, Finance Managers, and Members.
- Dynamic Sidebar:** The navigation menu automatically adjusts based on user roles and system settings.
- Secure Authentication:** Robust login, registration, and session management.

 🏛️ Society & Membership Management
- Centralized Directory:** Public listing of all active university societies.
- Application Workflow:** Seamless handling of membership requests with approval/rejection capabilities.
- Role Delegation:** Assign committee roles within societies to distribute responsibilities.

 📅 Event Orchestration
- Event Lifecycle:** From proposal and budget approval to execution and attendance tracking.
- Enrollment System:** Students can RSVP and manage their own event calendars.
- Volunteer Management:** Recruit and coordinate student volunteers for specific events.
- Sponsorship Tracking:** Manage financial and in-kind contributions from external partners.

💰 Financial Governance
- Budgeting: Allocate and track funds for specific societies and events.
- Transaction Ledger: Detailed recording of incomes and expenditures.

📜 Professional Certification
- Automated Generation:** Generate participation certificates for event attendees.
- Cryptographic Verification:** Secure verification system for authenticating certificates via unique hashes.


 🛠️ Technology Stack

| Layer | Technology |
| :--- | :--- |
| Frontend | HTML5, CSS3 (Antigravity Theme Engine), JavaScript |
| Backend | PHP 8.x |
| Database | MySQL |
| Development | XAMPP / Apache |

 📂 Project Architecture

The project follows a clean, modular structure for maximum maintainability:

```text
universal/
├── assets/             # Images, CSS, and Client-side JS
├── includes/           # Core Logic, Classes, and Shared Components
│   ├── auth.php        # Authentication middleware
│   ├── config.php      # System configuration
│   ├── db.php          # Database connection
│   └── [Classes].php   # Modular logic (Events, Clubs, Finance, etc.)
├── modules/            # Feature-specific pages and modules
│   ├── dashboards/     # Role-specific dashboard interfaces
│   ├── clubs/          # Society management modules
│   ├── events/         # Event management modules
│   ├── finance/        # Financial tracking modules
│   └── [Other]/        # Announcements, Certificates, Notifications
├── uploads/            # User-uploaded assets (images, receipts)
├── database.sql        # Core database schema
└── index.php           # Main entry point (Dynamic Dashboard Loader)


 ⚙️ Installation & Setup

 1️⃣ Prerequisites
- Install **XAMPP** (or any LAMP/WAMP stack with PHP 7.4+ and MySQL).
- Ensure **Apache** and **MySQL** services are running.

 2️⃣ Clone/Move Files
1. Download or clone the repository.
2. Move the `universal` folder to your server's root directory (e.g., `C:\xampp\htdocs\universal`).

 3️⃣ Database Configuration
1. Open **phpMyAdmin** (`http://localhost/phpmyadmin`).
2. Create a new database named `universal_db`.
3. Select the database and import the `database.sql` file located in the root directory.

 4️⃣ System Configuration
Open `includes/config.php` and verify the settings:
```php
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'universal_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('BASE_URL', 'http://localhost/universal/');
```

 5️⃣ Launch
Navigate to `http://localhost/universal` in your web browser.

 🔐 Default Access Credentials

| Role | Email | Password |
| Super Admin | `admin@gmail.com` | `123456` |
| General Student | `student@gmail.com` | `123456` |

---

 🚀 Roadmap & Future Enhancements
System Audit Logs: Detailed tracking of administrative actions.
Real-time Notifications:** WebSocket integration for instant alerts.
Payment Integration:** Stripe/PayPal support for membership fees.
Mobile Application:** Dedicated React Native app for on-the-go management.
Advanced Analytics:** Data visualization for society performance and engagement.


 👩‍💻 Developed By
Laiba Liaqat
- 📩 Email: [laibaliaqat8349@gmail.com](mailto:laibaliaqat8349@gmail.com)

---
*If you find this project useful, please consider giving it a ⭐ on GitHub!*
