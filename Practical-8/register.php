<?php

date_default_timezone_set("Asia/Kolkata");

require "db.php";

$message = "";
$messageType = "";

$enrollment_number = "";
$name = "";
$email = "";
$mobile = "";
$course = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $enrollment_number = trim($_POST["enrollment_number"] ?? "");
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $mobile = trim($_POST["mobile"] ?? "");
    $course = trim($_POST["course"] ?? "");
    $password = $_POST["password"] ?? "";

    if (
        $enrollment_number === "" ||
        $name === "" ||
        $email === "" ||
        $mobile === "" ||
        $course === "" ||
        $password === ""
    ) {

        $message = "All fields are required.";
        $messageType = "error";

    } elseif (
        !preg_match(
            "/^[A-Za-z0-9\/-]{2,50}$/",
            $enrollment_number
        )
    ) {

        $message = "Enter a valid enrollment number.";
        $messageType = "error";

    } elseif (
        !preg_match(
            "/^[A-Za-z ]{2,50}$/",
            $name
        )
    ) {

        $message = "Name must contain only letters and spaces.";
        $messageType = "error";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "Enter a valid email address.";
        $messageType = "error";

    } elseif (!preg_match("/^[0-9]{10}$/", $mobile)) {

        $message = "Mobile number must contain exactly 10 digits.";
        $messageType = "error";

    } elseif (
        !in_array(
            $course,
            [
                "Computer Engineering",
                "Information Technology",
                "Computer Science",
                "Artificial Intelligence",
                "Data Science"
            ],
            true
        )
    ) {

        $message = "Please select a valid course.";
        $messageType = "error";

    } elseif (
        !preg_match(
            "/^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[@$!%*?&]).{8,}$/",
            $password
        )
    ) {

        $message = "Password must contain at least 8 characters, uppercase, lowercase, number and special character.";
        $messageType = "error";

    } else {

        try {

            $stmt = $pdo->prepare(
                "SELECT enrollment_number
                 FROM students
                 WHERE enrollment_number = :enrollment_number
                 LIMIT 1"
            );

            $stmt->execute([
                ":enrollment_number" => $enrollment_number
            ]);

            if ($stmt->fetch()) {

                $message = "This enrollment number is already registered.";
                $messageType = "error";

            } else {

                $stmt = $pdo->prepare(
                    "SELECT email
                     FROM students
                     WHERE email = :email
                     LIMIT 1"
                );

                $stmt->execute([
                    ":email" => $email
                ]);

                if ($stmt->fetch()) {

                    $message = "This email is already registered.";
                    $messageType = "error";

                } else {

                    $passwordHash = password_hash(
                        $password,
                        PASSWORD_DEFAULT
                    );

                    $stmt = $pdo->prepare(
                        "INSERT INTO students
                        (
                            enrollment_number,
                            name,
                            email,
                            mobile,
                            course,
                            password_hash,
                            registration_date
                        )
                        VALUES
                        (
                            :enrollment_number,
                            :name,
                            :email,
                            :mobile,
                            :course,
                            :password_hash,
                            :registration_date
                        )"
                    );

                    $stmt->execute([

                        ":enrollment_number" => $enrollment_number,
                        ":name" => $name,
                        ":email" => $email,
                        ":mobile" => $mobile,
                        ":course" => $course,
                        ":password_hash" => $passwordHash,
                        ":registration_date" => date("Y-m-d H:i:s")

                    ]);

                    $message = "Registration saved successfully.";
                    $messageType = "success";

                    $enrollment_number = "";
                    $name = "";
                    $email = "";
                    $mobile = "";
                    $course = "";
                }
            }

        } catch (PDOException $e) {

            if ($e->getCode() === "23000") {

                $message = "Enrollment number or email is already registered.";

            } else {

                $message = "Database error. Please try again.";

            }

            $messageType = "error";
        }
    }
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

    <title>StudentHub - Registration</title>

    <link
        rel="stylesheet"
        href="CSS/register.css"
    >

</head>

<body>

<header>

    <h1>
        StudentHub
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

</header>

<div class="container">

    <h2>
        Student Registration
    </h2>

    <?php if ($message !== ""): ?>

        <div class="<?php echo htmlspecialchars($messageType); ?>">

            <?php
            echo htmlspecialchars($message);
            ?>

        </div>

    <?php endif; ?>

    <form
        method="POST"
        id="registrationForm"
        novalidate
    >

        <label for="enrollment_number">
            Enrollment Number
        </label>

        <input
            type="text"
            name="enrollment_number"
            id="enrollment_number"
            placeholder="Enter Enrollment Number"
            maxlength="50"
            value="<?php echo htmlspecialchars($enrollment_number); ?>"
            required
        >

        <div
            class="error"
            id="enrollmentError"
        ></div>

        <label for="name">
            Name
        </label>

        <input
            type="text"
            name="name"
            id="name"
            placeholder="Enter Full Name"
            maxlength="50"
            value="<?php echo htmlspecialchars($name); ?>"
            required
        >

        <div
            class="error"
            id="nameError"
        ></div>

        <label for="email">
            Email
        </label>

        <input
            type="email"
            name="email"
            id="email"
            placeholder="Enter Email"
            value="<?php echo htmlspecialchars($email); ?>"
            required
        >

        <div
            class="error"
            id="emailError"
        ></div>

        <label for="mobile">
            Mobile
        </label>

        <input
            type="text"
            name="mobile"
            id="mobile"
            placeholder="Enter 10 Digit Mobile Number"
            maxlength="10"
            inputmode="numeric"
            value="<?php echo htmlspecialchars($mobile); ?>"
            required
        >

        <div
            class="error"
            id="mobileError"
        ></div>

        <label for="course">
            Course
        </label>

        <select
            name="course"
            id="course"
            required
        >

            <option value="">
                Select Course
            </option>

            <option
                value="Computer Engineering"
                <?php
                echo ($course === "Computer Engineering")
                    ? "selected"
                    : "";
                ?>
            >
                Computer Engineering
            </option>

            <option
                value="Information Technology"
                <?php
                echo ($course === "Information Technology")
                    ? "selected"
                    : "";
                ?>
            >
                Information Technology
            </option>

            <option
                value="Computer Science"
                <?php
                echo ($course === "Computer Science")
                    ? "selected"
                    : "";
                ?>
            >
                Computer Science
            </option>

            <option
                value="Artificial Intelligence"
                <?php
                echo ($course === "Artificial Intelligence")
                    ? "selected"
                    : "";
                ?>
            >
                Artificial Intelligence
            </option>

            <option
                value="Data Science"
                <?php
                echo ($course === "Data Science")
                    ? "selected"
                    : "";
                ?>
            >
                Data Science
            </option>

        </select>

        <div
            class="error"
            id="courseError"
        ></div>

        <label for="password">
            Password
        </label>

        <input
            type="password"
            name="password"
            id="password"
            placeholder="Enter Password"
            required
        >

        <div
            class="error"
            id="passwordError"
        ></div>

        <button type="submit">
            Register
        </button>

    </form>

</div>

<script>

document
.getElementById("registrationForm")
.addEventListener("submit", function(event) {

    let valid = true;

    document
    .querySelectorAll(".error")
    .forEach(function(error) {

        error.textContent = "";

    });

    const enrollment =
        document
        .getElementById("enrollment_number")
        .value
        .trim();

    const name =
        document
        .getElementById("name")
        .value
        .trim();

    const email =
        document
        .getElementById("email")
        .value
        .trim();

    const mobile =
        document
        .getElementById("mobile")
        .value
        .trim();

    const course =
        document
        .getElementById("course")
        .value;

    const password =
        document
        .getElementById("password")
        .value;

    if (enrollment === "") {

        document
        .getElementById("enrollmentError")
        .textContent =
            "Enrollment number is required.";

        valid = false;

    } else if (
        !/^[A-Za-z0-9\/-]{2,50}$/.test(enrollment)
    ) {

        document
        .getElementById("enrollmentError")
        .textContent =
            "Enter a valid enrollment number.";

        valid = false;

    }

    if (name === "") {

        document
        .getElementById("nameError")
        .textContent =
            "Name is required.";

        valid = false;

    } else if (!/^[A-Za-z ]{2,50}$/.test(name)) {

        document
        .getElementById("nameError")
        .textContent =
            "Only letters and spaces are allowed.";

        valid = false;

    }

    if (email === "") {

        document
        .getElementById("emailError")
        .textContent =
            "Email is required.";

        valid = false;

    } else if (
        !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)
    ) {

        document
        .getElementById("emailError")
        .textContent =
            "Enter a valid email address.";

        valid = false;

    }

    if (mobile === "") {

        document
        .getElementById("mobileError")
        .textContent =
            "Mobile number is required.";

        valid = false;

    } else if (!/^[0-9]{10}$/.test(mobile)) {

        document
        .getElementById("mobileError")
        .textContent =
            "Enter exactly 10 digits.";

        valid = false;

    }

    if (course === "") {

        document
        .getElementById("courseError")
        .textContent =
            "Please select a course.";

        valid = false;

    }

    if (password === "") {

        document
        .getElementById("passwordError")
        .textContent =
            "Password is required.";

        valid = false;

    } else if (
        !/^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[@$!%*?&]).{8,}$/
        .test(password)
    ) {

        document
        .getElementById("passwordError")
        .textContent =
            "Minimum 8 characters with uppercase, lowercase, number and special character.";

        valid = false;

    }

    if (!valid) {

        event.preventDefault();

    }

});

</script>

<footer>

    <p>
        © <?php echo date("Y"); ?> StudentHub.
        All Rights Reserved.
    </p>

</footer>

</body>

</html>