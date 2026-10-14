# Student Portal — Practicals 7–15

## Student flow
1. Open `http://localhost/112/index.php`.
2. First-time students select **Create Account**; existing students select **Student Login**.
3. After student login, the menu shows **Home, About Us, Contact, Events/Register Event, Update Profile**.

## Admin flow
1. Open **Admin Login** separately.
2. Demo admin username: `admin`; password: `Admin@123`.
3. After admin login, the menu shows **Dashboard, Manage Students, Manage Events, Audit Logs**.
4. Manage Students supports adding/searching/exporting, moving a student to Deleted Students, restoring, and permanent deletion.
5. Manage Events supports adding/editing, moving events to Deleted Events, restoring, and permanent deletion.

## Delete and restore behavior
- **Delete** moves the student/event to the trash. It disappears from student-facing pages; a deleted student's login is disabled. Student registrations are retained.
- **Restore** returns the record and re-enables a restored student's linked login.
- **Permanently Delete** is a separate action inside the trash and cannot be undone. Permanently deleting a student also deletes their linked student login and event registrations; permanently deleting an event removes its registrations.
- Actions are written to Audit Logs.

## Run with XAMPP
1. Back up your old `112` project folder.
2. Extract the ZIP into `C:\xampp\htdocs\112` so that `index.php` is directly inside that folder.
3. Start Apache and MySQL in XAMPP.
4. In phpMyAdmin, import `database.sql` (database: `studenthub_practical7_15`). If the database already exists, the connection file attempts to add the event `deleted_at` field and expand the student status enum automatically; if MySQL denies ALTER permissions, import the updated schema or add the field manually.
5. Visit `http://localhost/112/index.php`.

## Requirements
- PHP 8+ with PDO MySQL and Fileinfo enabled.
- XAMPP defaults are configured in `db.php` (`root` with blank password). Update these values if your MySQL credentials differ.
- Change the demo admin password before deploying anywhere public.


Contact Messages: students can send a message from Contact after login. Messages are stored in the `contact_messages` MySQL table and shown to admins under Admin Dashboard → Contact Messages. Admins can mark messages read/unread. The table is created automatically by `db.php`; it is also included in `database.sql`.
