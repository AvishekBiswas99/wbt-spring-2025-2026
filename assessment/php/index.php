<?php
session_start();

$emailErr = $passErr = $loginErr = "";
$email = $password = "";
$loginSuccess = false;

function cleanInput($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (empty($_POST["email"])) {
        $emailErr = "Email is required";
    } else {
        $email = cleanInput($_POST["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailErr = "Invalid email format";
        }
    }

    if (empty($_POST["password"])) {
        $passErr = "Password is required";
    } else {
        $password = cleanInput($_POST["password"]);
    }

    if (!$emailErr && !$passErr) {
        $isValidUser = false;

        if (isset($_SESSION['users']) && isset($_SESSION['users'][$email])) {
            if ($_SESSION['users'][$email] === $password) {
                $isValidUser = true;
            }
        }

        if ($isValidUser) {
            $loginSuccess = true;
        } else {
            $loginErr = "Invalid email or password";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="form-container">
    <h2>Welcome Back</h2>
    <p class="subtitle">Log in to continue</p>

    <?php if ($loginErr): ?>
        <p style="color: red; font-size: 14px; margin-bottom: 15px; text-align: center;"><?= $loginErr ?></p>
    <?php endif; ?>

    <form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" novalidate>
        
        <div class="form-group">
            <label for="email">Email <span class="error"><?= $emailErr ?></span></label>
            <input type="text" id="email" name="email" value="<?= $email ?>">
        </div>

        <div class="form-group">
            <label for="password">Password <span class="error"><?= $passErr ?></span></label>
            <input type="password" id="password" name="password" value="<?= $password ?>">
        </div>

        <button type="submit" class="btn-submit">Log In</button>

    </form>

    <div class="auth-links" style="margin-top: 20px;">
        Don't have an account? <a href="register.php">Register here</a>
    </div>

    <?php if ($loginSuccess): ?>
        <div class="success-box">
            <strong>Login Successful!</strong><br>
            Welcome back, <?= $email ?>.
        </div>
    <?php endif; ?>
</div>

</body>
</html>