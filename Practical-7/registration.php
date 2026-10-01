<?php
date_default_timezone_set("Asia/Kolkata");

$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $mobile = trim($_POST["mobile"]);
    $course = trim($_POST["course"]);
    $password = $_POST["password"];

    if ($name == "" || $email == "" || $mobile == "" || $course == "" || $password == "") {
        $message = "All fields are required.";
        $messageType = "error";
    } elseif (!preg_match("/^[A-Za-z ]{2,50}$/", $name)) {
        $message = "Name must contain only letters and spaces.";
        $messageType = "error";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Enter a valid email address.";
        $messageType = "error";
    } elseif (!preg_match("/^[0-9]{10}$/", $mobile)) {
        $message = "Mobile number must contain exactly 10 digits.";
        $messageType = "error";
    } elseif (!in_array($course, ["Computer Engineering", "Information Technology", "Computer Science", "Artificial Intelligence", "Data Science"])) {
        $message = "Please select a valid course.";
        $messageType = "error";
    } elseif (!preg_match("/^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[@$!%*?&]).{8,}$/", $password)) {
        $message = "Password must contain 8 characters, uppercase, lowercase, number and special character.";
        $messageType = "error";
    } else {
        $name = htmlspecialchars($name, ENT_QUOTES, "UTF-8");
        $email = htmlspecialchars($email, ENT_QUOTES, "UTF-8");
        $mobile = htmlspecialchars($mobile, ENT_QUOTES, "UTF-8");
        $course = htmlspecialchars($course, ENT_QUOTES, "UTF-8");

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $date = date("d-m-Y h:i:s A");

        $csvFile = "registrations.csv";

        if (!file_exists($csvFile)) {
            $file = fopen($csvFile, "w");
            fputcsv($file, ["Name", "Email", "Mobile", "Course", "Password Hash", "Date"]);
            fclose($file);
        }

        $file = fopen($csvFile, "a");
        fputcsv($file, [$name, $email, $mobile, $course, $passwordHash, $date]);
        fclose($file);

        $jsonFile = "registrations.json";

        if (file_exists($jsonFile)) {
            $records = json_decode(file_get_contents($jsonFile), true);
            if (!is_array($records)) {
                $records = [];
            }
        } else {
            $records = [];
        }

        $records[] = [
            "name" => $name,
            "email" => $email,
            "mobile" => $mobile,
            "course" => $course,
            "password_hash" => $passwordHash,
            "date" => $date
        ];

        file_put_contents($jsonFile, json_encode($records, JSON_PRETTY_PRINT), LOCK_EX);

        $message = "Registration saved successfully in CSV and JSON.";
        $messageType = "success";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration</title>
    <link rel="stylesheet" href="CSS/style.css">
</head>
<body>

<header>
    <h1>Registration Form</h1>
</header>

<div class="container">
    <form method="POST" action="" id="registrationForm">
        <label>Name</label>
        <input type="text" name="name" id="name" required>
        <div class="error" id="nameError"></div>

        <label>Email</label>
        <input type="email" name="email" id="email" required>
        <div class="error" id="emailError"></div>

        <label>Mobile</label>
        <input type="text" name="mobile" id="mobile" maxlength="10" required>
        <div class="error" id="mobileError"></div>

        <label>Course</label>
        <select name="course" id="course" required>
            <option value="">Select Course</option>
            <option value="Computer Engineering">Computer Engineering</option>
            <option value="Information Technology">Information Technology</option>
            <option value="Computer Science">Computer Science</option>
            <option value="Artificial Intelligence">Artificial Intelligence</option>
            <option value="Data Science">Data Science</option>
        </select>
        <div class="error" id="courseError"></div>

        <label>Password</label>
        <input type="password" name="password" id="password" required>
        <div class="error" id="passwordError"></div>

        <button type="submit">Register</button>
    </form>

    <?php if ($message != "") { ?>
        <div class="<?php echo $messageType; ?>">
            <?php echo $message; ?>
        </div>
    <?php } ?>
</div>

<script>
document.getElementById("registrationForm").addEventListener("submit", function(event) {
    let valid = true;

    document.querySelectorAll(".error").forEach(function(error) {
        error.innerHTML = "";
    });

    let name = document.getElementById("name").value.trim();
    let email = document.getElementById("email").value.trim();
    let mobile = document.getElementById("mobile").value.trim();
    let course = document.getElementById("course").value;
    let password = document.getElementById("password").value;

    if (name === "") {
        document.getElementById("nameError").innerHTML = "Name is required.";
        valid = false;
    } else if (!/^[A-Za-z ]{2,50}$/.test(name)) {
        document.getElementById("nameError").innerHTML = "Only letters and spaces are allowed.";
        valid = false;
    }

    if (email === "") {
        document.getElementById("emailError").innerHTML = "Email is required.";
        valid = false;
    }

    if (mobile === "") {
        document.getElementById("mobileError").innerHTML = "Mobile number is required.";
        valid = false;
    } else if (!/^[0-9]{10}$/.test(mobile)) {
        document.getElementById("mobileError").innerHTML = "Enter exactly 10 digits.";
        valid = false;
    }

    if (course === "") {
        document.getElementById("courseError").innerHTML = "Course is required.";
        valid = false;
    }

    if (password === "") {
        document.getElementById("passwordError").innerHTML = "Password is required.";
        valid = false;
    } else if (!/^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[@$!%*?&]).{8,}$/.test(password)) {
        document.getElementById("passwordError").innerHTML = "Minimum 8 characters with uppercase, lowercase, number and special character.";
        valid = false;
    }

    if (!valid) {
        event.preventDefault();
    }
});
</script>

</body>
</html>