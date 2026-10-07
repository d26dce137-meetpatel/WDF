<?php 

require_once "auth.php"; 

$message = ""; 

if (isset($_SESSION['login_error'])) { 
    $message = $_SESSION['login_error']; 
    unset($_SESSION['login_error']); 
} 

if (isset($_GET['timeout'])) { 
    $message = "Session expired. Please login again."; 
} 

if ($_SERVER["REQUEST_METHOD"] == "POST") { 

    $username = trim($_POST['username']); 
    $password = $_POST['password']; 

    if ($username == "" || $password == "") { 

        $message = "Please enter username and password."; 

    } else { 

        $stmt = $pdo->prepare(
            "SELECT * FROM users WHERE username = ?"
        ); 

        $stmt->execute([$username]); 

        $user = $stmt->fetch(PDO::FETCH_ASSOC); 

        if ($user && password_verify($password, $user['password'])) { 

            session_regenerate_id(true); 

            $_SESSION['user_id'] = $user['id']; 
            $_SESSION['username'] = $user['username']; 
            $_SESSION['role'] = $user['role']; 
            $_SESSION['last_activity'] = time(); 

            $update = $pdo->prepare(
                "UPDATE users SET last_login = NOW() WHERE id = ?"
            ); 

            $update->execute([$user['id']]); 

            if (isset($_POST['remember'])) { 

                $token = bin2hex(random_bytes(32)); 
                $token_hash = hash('sha256', $token); 

                $expires = date(
                    "Y-m-d H:i:s",
                    time() + (30 * 24 * 60 * 60)
                ); 

                $stmt = $pdo->prepare("
                    INSERT INTO remember_tokens
                    (user_id, token_hash, expires_at)
                    VALUES (?, ?, ?)
                "); 

                $stmt->execute([
                    $user['id'],
                    $token_hash,
                    $expires
                ]); 

                setcookie(
                    "remember_token",
                    $token,
                    [
                        "expires" => time() + (30 * 24 * 60 * 60),
                        "path" => "/",
                        "httponly" => true,
                        "samesite" => "Lax"
                    ]
                ); 
            } 

            header("Location: dashboard.php"); 
            exit; 

        } else { 

            $_SESSION['login_error'] = "Invalid username or password."; 
            header("Location: login.php"); 
            exit; 
        } 
    } 
} 

?> 

<!DOCTYPE html> 
<html> 

<head> 

    <title>Login</title> 

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f6f9;

            min-height: 100vh;

            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-box {
            width: 450px;
            padding: 35px;

            background: white;

            border-radius: 12px;

            box-shadow: 0 4px 15px rgba(0,0,0,0.12);
        }

        h2 {
            text-align: center;
            color: #075294;
            margin-bottom: 30px;
        }

        label {
            display: block;
            margin-bottom: 8px;

            font-size: 16px;
            color: #333;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            height: 45px;

            padding: 10px 14px;
            margin-bottom: 20px;

            border: 1px solid #ccc;
            border-radius: 6px;

            font-size: 16px;
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #075294;
        }

        .remember {
            display: flex;
            align-items: center;
            gap: 8px;

            margin-bottom: 20px;
        }

        .remember input {
            width: auto;
            margin: 0;
        }

        button {
            width: 100%;
            height: 45px;

            border: none;
            border-radius: 6px;

            background: #075294;
            color: white;

            font-size: 17px;
            cursor: pointer;
        }

        button:hover {
            background: #063f73;
        }

        .message {
            padding: 10px;
            margin-bottom: 20px;

            border-radius: 6px;

            background: #ffeaea;
            color: #c62828;

            text-align: center;
        }

        @media (max-width: 600px) {

            .login-box {
                width: 90%;
                padding: 25px;
            }

        }

    </style>

</head> 

<body> 

<div class="login-box"> 

    <h2>Login</h2> 

    <?php if ($message != ""): ?> 

        <p class="message">
            <?php echo htmlspecialchars($message); ?>
        </p> 

    <?php endif; ?> 

    <form method="POST"> 

        <label>Username</label> 

        <input 
            type="text" 
            name="username" 
            required
        > 

        <label>Password</label> 

        <input 
            type="password" 
            name="password" 
            required
        > 

        <label class="remember">

            <input 
                type="checkbox" 
                name="remember"
            > 

            Remember Me

        </label> 

        <button type="submit">
            Login
        </button> 

    </form> 

</div> 

</body> 
</html>
