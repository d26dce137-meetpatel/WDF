<?php 

require_once "auth.php"; 

requireLogin(); 

?> 

<!DOCTYPE html> 
<html> 

<head> 

    <title>Dashboard</title> 

    <style> 

        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            margin: 0;
            text-align: center;
        }

        .box {
            width: 500px;
            margin: 100px auto;
            background: white;
            padding: 35px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.12);
        }

        h1 {
            color: #333;
            margin-bottom: 10px;
        }

        h2 {
            color: #007bff;
            margin-top: 10px;
        }

        p {
            color: #666;
            font-size: 16px;
        }

        .role {
            color: #333;
        }

        a {
            display: block;
            margin: 15px 0;
            padding: 11px;
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

    <h1>Welcome</h1> 

    <h2>
        <?php echo htmlspecialchars($_SESSION['username']); ?> 
    </h2> 

    <p class="role">
        Role:
        <strong>
            <?php echo htmlspecialchars($_SESSION['role']); ?> 
        </strong>
    </p> 


    <?php if ($_SESSION['role'] == "admin"): ?> 

        <a href="admin.php">
            Admin Dashboard
        </a> 

    <?php endif; ?> 


    <?php if ($_SESSION['role'] == "student"): ?> 

        <a href="student.php">
            Student Dashboard
        </a> 

    <?php endif; ?> 


    <a href="logout.php" class="logout">
        Logout
    </a> 

</div> 

</body> 
</html>
