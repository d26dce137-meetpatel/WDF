<?php

require "db.php";

$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $enrollment_number = trim($_POST["enrollment_number"] ?? "");
    $event_id = trim($_POST["event_id"] ?? "");

    if ($enrollment_number == "" || $event_id == "") {

        $message = "Enrollment Number and Event are required.";
        $messageType = "error";

    }

    else {

        $stmt = $pdo->prepare(
            "SELECT enrollment_number
             FROM students
             WHERE enrollment_number = :enrollment_number"
        );

        $stmt->execute([
            ":enrollment_number" => $enrollment_number
        ]);


        if (!$stmt->fetch()) {

            $message = "Enrollment Number does not exist.";
            $messageType = "error";

        }

        else {

            $stmt = $pdo->prepare(
                "SELECT
                    event_name,
                    event_date,
                    venue
                 FROM events
                 WHERE event_id = :event_id"
            );

            $stmt->execute([
                ":event_id" => $event_id
            ]);

            $event = $stmt->fetch(PDO::FETCH_ASSOC);


            if (!$event) {

                $message = "Event does not exist.";
                $messageType = "error";

            }

            else {

                $stmt = $pdo->prepare(
                    "SELECT enrollment_number
                     FROM registrations
                     WHERE enrollment_number = :enrollment_number
                     AND event_name = :event_name"
                );

                $stmt->execute([

                    ":enrollment_number" =>
                        $enrollment_number,

                    ":event_name" =>
                        $event["event_name"]

                ]);


                if ($stmt->fetch()) {

                    $message =
                        "Student is already registered for this event.";

                    $messageType = "error";

                }

                else {

                    $stmt = $pdo->prepare(
                        "INSERT INTO registrations
                        (
                            enrollment_number,
                            event_name,
                            event_date,
                            venue
                        )
                        VALUES
                        (
                            :enrollment_number,
                            :event_name,
                            :event_date,
                            :venue
                        )"
                    );

                    $stmt->execute([

                        ":enrollment_number" =>
                            $enrollment_number,

                        ":event_name" =>
                            $event["event_name"],

                        ":event_date" =>
                            $event["event_date"],

                        ":venue" =>
                            $event["venue"]

                    ]);


                    $message =
                        "Event registration successful.";

                    $messageType = "success";

                }
            }
        }
    }
}

$eventSearch =
    isset($_GET["event_search"])
    ? trim($_GET["event_search"])
    : "";


if ($eventSearch != "") {

    $stmt = $pdo->prepare(
        "SELECT
            event_id,
            event_name,
            event_date,
            venue
         FROM events
         WHERE event_name LIKE :search
         OR venue LIKE :search
         OR event_date LIKE :search
         ORDER BY event_date"
    );

    $stmt->execute([

        ":search" =>
            "%" . $eventSearch . "%"

    ]);

    $events =
        $stmt->fetchAll(PDO::FETCH_ASSOC);

}

else {

    $events =
        $pdo->query(
            "SELECT
                event_id,
                event_name,
                event_date,
                venue
             FROM events
             ORDER BY event_date"
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

    <title>StudentHub Events</title>

    <link
        rel="stylesheet"
        href="CSS/event.css"
    >

</head>


<body>

<header>

    <div>

        <h1>
            StudentHub
        </h1>

    </div>


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

</header>



<div class="container">

    <?php if ($message != "") { ?>

        <div class="<?php echo htmlspecialchars($messageType); ?>">

            <?php
            echo htmlspecialchars($message);
            ?>

        </div>

    <?php } ?>

    <section>

        <h2>
            Available Events
        </h2>

        <form
            method="GET"
            class="search-form"
        >

            <input
                type="text"
                name="event_search"
                placeholder="Search event, date or venue"
                value="<?php
                    echo htmlspecialchars($eventSearch);
                ?>"
            >


            <button type="submit">
                Search
            </button>


            <a
                href="event.php"
                class="clear-btn"
            >
                Clear
            </a>

        </form>

        <div class="event-grid">


            <?php if (count($events) > 0) { ?>


                <?php foreach ($events as $event) { ?>


                    <div class="event-card">


                        <h3>

                            <?php

                            echo htmlspecialchars(
                                $event["event_name"]
                            );

                            ?>

                        </h3>


                        <p>

                            <strong>
                                Date:
                            </strong>

                            <?php

                            echo htmlspecialchars(
                                $event["event_date"]
                            );

                            ?>

                        </p>


                        <p>

                            <strong>
                                Venue:
                            </strong>

                            <?php

                            echo htmlspecialchars(
                                $event["venue"]
                            );

                            ?>

                        </p>


                    </div>


                <?php } ?>


            <?php } else { ?>


                <p class="no-data">

                    No events found.

                </p>


            <?php } ?>


        </div>

    </section>

    <section class="registration-section">

        <h2>
            Register for Event
        </h2>


        <form
            method="POST"
            class="registration-form"
        >

            <label>
                Enrollment Number
            </label>


            <input
                type="text"
                name="enrollment_number"
                placeholder="Enter Enrollment Number"
                maxlength="50"
                required
            >

            <label>
                Select Event
            </label>


            <select
                name="event_id"
                required
            >

                <option value="">
                    Select Event
                </option>


                <?php foreach ($events as $event) { ?>


                    <option
                        value="<?php
                            echo htmlspecialchars(
                                $event["event_id"]
                            );
                        ?>"
                    >

                        <?php

                        echo htmlspecialchars(
                            $event["event_name"]
                        );

                        ?>

                    </option>


                <?php } ?>


            </select>

            <button type="submit">

                Register for Event

            </button>


        </form>

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