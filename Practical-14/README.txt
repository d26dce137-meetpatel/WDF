PRACTICAL 14 — ADMIN DASHBOARD WITH RBAC AND AUDIT LOG

Requirements:
- XAMPP (Apache + MySQL)
- PHP 8+ with PDO MySQL
- Existing StudentHub database can be used, but verify schema/column names.

SETUP:
1. Copy the entire Practical14_Admin_Dashboard folder into:
   C:\xampp\htdocs\
2. Start Apache and MySQL from XAMPP Control Panel.
3. Open phpMyAdmin: http://localhost/phpmyadmin
4. Import database.sql. If you already have students/events/registrations tables, back up first and adapt database.sql to avoid duplicate/demo rows.
5. Check includes/db.php database name, username and password. Default DB is practical14_admin, user root, blank password.
6. Open http://localhost/Practical14_Admin_Dashboard/create_admin.php once.
   Demo admin login: admin@example.com / Admin@12345
7. DELETE create_admin.php after creating the admin account.
8. Open http://localhost/Practical14_Admin_Dashboard/login.php and log in.

FEATURES:
- Dynamic database counts: users, students, events, registrations.
- Latest registrations table.
- Server-side admin-only protection with require_admin().
- Dynamic menu based on session role.
- Login/logout and page-view audit records.
- Audit log page shows latest 200 actions.
- Password verification uses password_hash/password_verify.
- Session ID regenerated after login.

IMPORTANT:
- If your existing StudentHub uses different column names (e.g. student_name instead of name), update SQL in index.php and registrations.php.
- Keep create_admin.php deleted after initial setup.
- For a real deployment, use HTTPS, CSRF protection, strong credentials, and restricted database permissions.

SUBMISSION / TEST CASES:
1. Admin login -> dashboard counts appear from database.
2. Student login -> student home appears; admin links are not shown.
3. Logged-in student opens index.php directly -> HTTP 403.
4. Admin opens Audit Logs -> VIEW_AUDIT_LOGS record appears.
5. Login/logout -> LOGIN and LOGOUT records appear.
6. Add registration in database -> it appears under Latest Registrations.
