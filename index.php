<?php
session_start();
// Redirect if already logged in
if (isset($_SESSION['userid'])) {
    header("Location: dashboard.php");
    exit();
}
// Check for login errors from authenticate.php
$error = $_SESSION['error'] ?? '';
unset($_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome to the CMS</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Specific styles for the landing page */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: url('https://images.unsplash.com/photo-1542435503-956c469947f6') no-repeat center center fixed;
            background-size: cover;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            position: relative;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6); /* Dark overlay */
            z-index: 1;
        }

        .landing-container {
            position: relative;
            z-index: 2;
            text-align: center;
            max-width: 500px;
            padding: 40px;
            background-color: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .landing-container h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .landing-container p {
            font-size: 1.1rem;
            margin-bottom: 30px;
            font-weight: 300;
        }

        .login-form-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .login-form-container h2 {
            color: #333;
            margin-top: 0;
            font-size: 1.5rem;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
        }

        .login-form-container .form-group {
            text-align: left;
            margin-bottom: 20px;
        }

        .login-form-container label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: bold;
        }

        .login-form-container input[type="text"],
        .login-form-container input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-sizing: border-box;
            transition: border-color 0.3s;
        }

        .login-form-container input[type="text"]:focus,
        .login-form-container input[type="password"]:focus {
            border-color: #3498db;
            outline: none;
        }

        .login-form-container button {
            width: 100%;
            padding: 12px;
            background-color: #2ecc71;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .login-form-container button:hover {
            background-color: #27ae60;
        }
        
        .error {
            color: #e74c3c;
            font-weight: bold;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="landing-container">
        <h1>Welcome to the Content Management System</h1>
        <p>Your platform for creating, managing, and publishing content effortlessly. Please sign in to continue.</p>

        <div class="login-form-container">
            <h2><i class="fas fa-lock"></i> Sign In</h2>
            <?php if ($error): ?>
                <p class="error"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>
            <form action="authenticate.php" method="POST">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit">Sign In</button>
            </form>
        </div>
    </div>
</body>
</html>