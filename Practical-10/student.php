<?php 

require_once "auth.php"; 

requireRole("student"); 

?> 

<!DOCTYPE html> 
<html> 

<head> 

    <title>Student Dashboard</title> 

    <style> 

        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            margin: 0;
        }

        .box {
            width: 500px;
            margin: 100px auto;
            background: white;
            padding: 35px;
            text-align: center;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.12);
        }

        h1 {
            color: #333;
            margin-bottom: 20px;
        }

        p {
            color: #666;
            font-size: 16px;
        }

        .username {
            color: #007bff;
            font-weight: bold;
        }

        .buttons {
            margin-top: 25px;
        }

        a {
            display: inline-block;
            padding: 11px 22px;
            margin: 5px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            transition: 0.2s;
        }

        a:hover {
            opacity: 0.85;
        }

        .logout {
            background: #dc3545;
        }

    </style> 

</head> 

<body> 

<div class="box"> 

    <h1>Student Dashboard</h1> 

    <p>
        Welcome Student,
        <span class="username">
            <?php echo htmlspecialchars($_SESSION['username']); ?>
        </span>
    </p>

    <p>
        You have student access.
    </p>

    <div class="buttons">

        <a href="dashboard.php">
            Back
        </a>

        <a href="logout.php" class="logout">
            Logout
        </a>

    </div>

</div> 

</body> 

</html>