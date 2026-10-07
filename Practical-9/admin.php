<?php

require "db.php";

$totalStudents = $pdo->query(
    "SELECT COUNT(*) FROM students"
)->fetchColumn();

$totalEvents = $pdo->query(
    "SELECT COUNT(*) FROM events"
)->fetchColumn();

$totalRegistrations = $pdo->query(
    "SELECT COUNT(*) FROM registrations"
)->fetchColumn();

$search = isset($_GET["search"])
    ? trim($_GET["search"])
    : "";

if ($search != "") {

    $stmt = $pdo->prepare(
        "SELECT
            enrollment_number,
            name,
            email,
            mobile,
            course,
            registration_date
         FROM students
         WHERE enrollment_number LIKE :search
            OR name LIKE :search
            OR email LIKE :search
            OR mobile LIKE :search
            OR course LIKE :search
         ORDER BY enrollment_number ASC"
    );

    $stmt->execute([
        ":search" => "%" . $search . "%"
    ]);

    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

} else {

    $students = $pdo->query(
        "SELECT
            enrollment_number,
            name,
            email,
            mobile,
            course,
            registration_date
         FROM students
         ORDER BY enrollment_number ASC"
    )->fetchAll(PDO::FETCH_ASSOC);
}

if ($search != "") {

    $stmt = $pdo->prepare(
        "SELECT
            event_id,
            event_name,
            event_date,
            venue
         FROM events
         WHERE event_name LIKE :search
            OR event_date LIKE :search
            OR venue LIKE :search
         ORDER BY event_date ASC"
    );

    $stmt->execute([
        ":search" => "%" . $search . "%"
    ]);

    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

} else {

    $events = $pdo->query(
        "SELECT
            event_id,
            event_name,
            event_date,
            venue
         FROM events
         ORDER BY event_date ASC"
    )->fetchAll(PDO::FETCH_ASSOC);
}

if ($search != "") {

    $stmt = $pdo->prepare(
        "SELECT
            enrollment_number,
            event_name,
            event_date,
            venue
         FROM registrations
         WHERE enrollment_number LIKE :search
            OR event_name LIKE :search
            OR event_date LIKE :search
            OR venue LIKE :search
         ORDER BY event_date ASC"
    );

    $stmt->execute([
        ":search" => "%" . $search . "%"
    ]);

    $registrations = $stmt->fetchAll(PDO::FETCH_ASSOC);

} else {

    $registrations = $pdo->query(
        "SELECT
            enrollment_number,
            event_name,
            event_date,
            venue
         FROM registrations
         ORDER BY event_date ASC"
    )->fetchAll(PDO::FETCH_ASSOC);
}

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>StudentHub Admin</title>

    <link
        rel="stylesheet"
        href="CSS/admin.css"
    >

</head>

<body>

<header class="admin-header">

    <div class="admin-header-content">

        <h1>
            StudentHub Admin
        </h1>

        <nav>

            <a href="index.php">
                Home
            </a>

            <a href="register.php">
                Registration
            </a>

            <a href="event.php">
                Events
            </a>

            <a href="admin.php">
                Admin
            </a>

        </nav>

    </div>

</header>

<div class="admin-container">

    <section class="dashboard">

        <div class="dashboard-card">

            <div class="dashboard-icon">
                👨‍🎓
            </div>

            <h3>
                Total Students
            </h3>

            <strong>
                <?php
                echo htmlspecialchars($totalStudents);
                ?>
            </strong>

        </div>

        <div class="dashboard-card">

            <div class="dashboard-icon">
                📅
            </div>

            <h3>
                Total Events
            </h3>

            <strong>
                <?php
                echo htmlspecialchars($totalEvents);
                ?>
            </strong>

        </div>

        <div class="dashboard-card">

            <div class="dashboard-icon">
                📝
            </div>

            <h3>
                Event Registrations
            </h3>

            <strong>
                <?php
                echo htmlspecialchars($totalRegistrations);
                ?>
            </strong>

        </div>

    </section>

    <section class="admin-section">

        <h2>
            Search Admin Data
        </h2>

        <form
            method="GET"
            class="search-form"
        >

            <input
                type="text"
                name="search"
                placeholder="Search enrollment, student, event, course, date or venue"
                value="<?php echo htmlspecialchars($search); ?>"
            >

            <button type="submit">
                Search
            </button>

            <a
                href="admin.php"
                class="clear-btn"
            >
                Clear
            </a>

        </form>

    </section>

    <section class="admin-section">

        <h2>
            Registered Students
        </h2>

        <div class="table-container">

            <table>

                <thead>

                    <tr>

                        <th>
                            Enrollment Number
                        </th>

                        <th>
                            Name
                        </th>

                        <th>
                            Email
                        </th>

                        <th>
                            Mobile
                        </th>

                        <th>
                            Course
                        </th>

                        <th>
                            Registration Date
                        </th>

                    </tr>

                </thead>

                <tbody>

                <?php if (count($students) > 0) { ?>

                    <?php foreach ($students as $student) { ?>

                        <tr>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $student["enrollment_number"]
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $student["name"]
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $student["email"]
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $student["mobile"]
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $student["course"]
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $student["registration_date"]
                                );
                                ?>
                            </td>

                        </tr>

                    <?php } ?>

                <?php } else { ?>

                    <tr>

                        <td
                            colspan="6"
                            class="no-data"
                        >

                            No students found.

                        </td>

                    </tr>

                <?php } ?>

                </tbody>

            </table>

        </div>

    </section>

    <section class="admin-section">

        <h2>
            Available Events
        </h2>

        <div class="table-container">

            <table>

                <thead>

                    <tr>

                        <th>
                            Event ID
                        </th>

                        <th>
                            Event Name
                        </th>

                        <th>
                            Event Date
                        </th>

                        <th>
                            Venue
                        </th>

                    </tr>

                </thead>

                <tbody>

                <?php if (count($events) > 0) { ?>

                    <?php foreach ($events as $event) { ?>

                        <tr>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $event["event_id"]
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $event["event_name"]
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $event["event_date"]
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $event["venue"]
                                );
                                ?>
                            </td>

                        </tr>

                    <?php } ?>

                <?php } else { ?>

                    <tr>

                        <td
                            colspan="4"
                            class="no-data"
                        >

                            No events found.

                        </td>

                    </tr>

                <?php } ?>

                </tbody>

            </table>

        </div>

    </section>

    <section class="admin-section">

        <h2>
            Event Registrations
        </h2>

        <div class="table-container">

            <table>

                <thead>

                    <tr>

                        <th>
                            Enrollment Number
                        </th>

                        <th>
                            Event Name
                        </th>

                        <th>
                            Event Date
                        </th>

                        <th>
                            Venue
                        </th>

                    </tr>

                </thead>

                <tbody>

                <?php if (count($registrations) > 0) { ?>

                    <?php foreach ($registrations as $registration) { ?>

                        <tr>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $registration["enrollment_number"]
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $registration["event_name"]
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $registration["event_date"]
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $registration["venue"]
                                );
                                ?>
                            </td>

                        </tr>

                    <?php } ?>

                <?php } else { ?>

                    <tr>

                        <td
                            colspan="4"
                            class="no-data"
                        >

                            No event registrations found.

                        </td>

                    </tr>

                <?php } ?>

                </tbody>

            </table>

        </div>

    </section>

</div>

<footer>

    <p>
        © <?php echo date("Y"); ?> StudentHub.
        All Rights Reserved.
    </p>

</footer>

</body>

</html>