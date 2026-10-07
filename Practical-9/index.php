<?php

date_default_timezone_set("Asia/Kolkata");

require "db.php";

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>StudentHub - Home</title>

    <link
        rel="stylesheet"
        href="CSS/index.css"
    >

</head>

<body>

<header class="main-header">

    <div class="logo">

        <h1>
            StudentHub
        </h1>

    </div>

    <nav>

        <a href="index.php">
            Home
        </a>

        <a href="register.php">
            Student Registration
        </a>

        <a href="event.php">
            Events
        </a>

        <a href="admin.php">
            Admin
        </a>

    </nav>

</header>

<main>

    <section class="hero">

        <div class="hero-content">

            <h1>
                Welcome to StudentHub
            </h1>

            <p>
                Manage student registration and college events
                easily from one place.
            </p>

        </div>

    </section>

    <section class="features">

        <h2>
            Our Services
        </h2>

        <div class="feature-grid">

            <div class="feature-card">

                <div class="icon">
                    👨‍🎓
                </div>

                <h3>
                    Student Registration
                </h3>

                <p>
                    Register students with enrollment number,
                    name, email, mobile number, course and password.
                </p>

                <a href="register.php">
                    Register Student →
                </a>

            </div>

            <div class="feature-card">

                <div class="icon">
                    📅
                </div>

                <h3>
                    College Events
                </h3>

                <p>
                    Search available college events,
                    check event date and venue and register.
                </p>

                <a href="event.php">
                    View Events →
                </a>

            </div>

            <div class="feature-card">

                <div class="icon">
                    🔐
                </div>

                <h3>
                    Admin Panel
                </h3>

                <p>
                    Admin can securely view student registrations
                    and event registration details.
                </p>

                <a href="admin.php">
                    Admin Panel →
                </a>

            </div>

        </div>

    </section>

</main>

<footer>

    <p>
        © <?php echo date("Y"); ?> StudentHub.
        All Rights Reserved.
    </p>

</footer>

</body>

</html>