PRACTICAL 12 - EVENT MANAGEMENT CRUD WITH POSTER UPLOAD

Requirements:
- XAMPP (Apache + MySQL)
- PHP 8+ recommended
- Browser

INSTALLATION:
1. Extract the folder Practical12_Event_Management_CRUD into:
   C:\xampp\htdocs\
2. Open XAMPP Control Panel and start Apache and MySQL.
3. Open http://localhost/phpmyadmin
4. Import database.sql (or create a database and import the SQL file).
5. Check config.php. Default XAMPP settings are:
   DB_HOST=localhost, DB_NAME=event_management, DB_USER=root, DB_PASS=
6. Open http://localhost/Practical12_Event_Management_CRUD/
7. Demo admin login:
   Username: admin
   Password: Admin@123
8. Use the Events page to add, view, edit, and delete events. Try uploading a JPG/PNG/WebP poster.
9. For validation screenshots, try a .txt file or a file larger than 2 MB.

FEATURES:
- Admin-only session access (login/logout)
- Add, list/view, edit/update, and delete events
- Poster upload validation by extension, MIME type, and size (max 2 MB)
- Randomized poster filenames
- Event category and open/closed status
- Success/error feedback messages
- PDO prepared statements and CSRF tokens
- Audit log for event changes
- SQL sample data

SECURITY NOTES:
- Change the demo admin password before using this beyond a classroom demonstration.
- The included admin account is a practical/demo account, not production authentication.
- Upload directory includes .htaccess restrictions for Apache. Do not deploy this demo unchanged to a public server.
