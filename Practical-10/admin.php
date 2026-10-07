<?php 

require_once "auth.php"; 

requireRole("admin"); 

?> 

<!DOCTYPE html> 
<html> 

<head> 

    <title>Admin Dashboard</title> 

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
            margin-bottom: 15px;
        }

        p {
            color: #666;
            font-size: 16px;
        }

        .buttons {
            margin-top: 25px;
        }

        a {
            display: inline-block;
            padding: 10px 22px;
            margin: 5px;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
        }

        .back {
            background: #007bff;
        }

        .logout {
            background: #dc3545;
        }

        a:hover {
            opacity: 0.85;
        }

    </style> 

</head> 

<body> 

<div class="box"> 

    <h1>Admin Dashboard</h1> 

    <p>
        Welcome Admin,
        <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
    </p>

    <p>
        You have administrator access.
    </p>

    <div class="buttons">

        <a href="dashboard.php" class="back">
            Back
        </a>

        <a href="logout.php" class="logout">
            Logout
        </a>

    </div>

</div> 

</body> 

</html>
