<?php
session_start();

$fnameErr = $lnameErr = $contactErr = $emailErr = $passErr = "";
$fname = $lname = $contact = $email = $password = "";
$registerSuccess = false;

function cleanInput($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (empty($_POST["fname"])) {
        $fnameErr = "First name is required";
    } else {
        $fname = cleanInput($_POST["fname"]);
        if (!preg_match("/^[a-zA-Z-' ]*$/", $fname)) {
            $fnameErr = "Only letters and white space allowed";
        }
    }

    if (empty($_POST["lname"])) {
        $lnameErr = "Last name is required";
    } else {
        $lname = cleanInput($_POST["lname"]);
        if (!preg_match("/^[a-zA-Z-' ]*$/", $lname)) {
            $lnameErr = "Only letters and white space allowed";
        }
    }

    if (empty($_POST["contact"])) {
        $contact = "";
    } else {
        $contact = cleanInput($_POST["contact"]);
        if (!preg_match("/^\+?[0-9]{10,15}$/", $contact)) {
            $contactErr = "Invalid format";
        }
    }

    if (empty($_POST["email"])) {
        $emailErr = "Email is required";
    } else {
        $email = cleanInput($_POST["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailErr = "Invalid format";
        }
    }

    if (empty($_POST["password"])) {
        $passErr = "Password is required";
    } else {
        $password = cleanInput($_POST["password"]);
        if (strlen($password) < 8) {
            $passErr = "Minimum 8 characters";
        }
    }

    if (!$fnameErr && !$lnameErr && !$contactErr && !$emailErr && !$passErr) {
        if (!isset($_SESSION['users'])) {
            $_SESSION['users'] = [];
        }
        
        $_SESSION['users'][$email] = $password;
        
        $registerSuccess = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registration</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="form-container">
    <h2>Create Account</h2>
    <p class="subtitle">Register to join us</p>

    <form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" novalidate>
        
        <div class="form-group">
            <label for="fname">First Name <span class="error"><?= $fnameErr ?></span></label>
            <input type="text" id="fname" name="fname" value="<?= $fname ?>">
        </div>

        <div class="form-group">
            <label for="lname">Last Name <span class="error"><?= $lnameErr ?></span></label>
            <input type="text" id="lname" name="lname" value="<?= $lname ?>">
        </div>

        <div class="form-group">
            <label for="contact">Contact Number (Optional) <span class="error"><?= $contactErr ?></span></label>
            <input type="text" id="contact" name="contact" value="<?= $contact ?>">
        </div>

        <div class="form-group">
            <label for="email">Email <span class="error"><?= $emailErr ?></span></label>
            <input type="text" id="email" name="email" value="<?= $email ?>">
        </div>

        <div class="form-group">
            <label for="password">Password <span class="error"><?= $passErr ?></span></label>
            <input type="password" id="password" name="password" value="<?= $password ?>">
        </div>

        <button type="submit" class="btn-submit">Register</button>

    </form>

    <div class="auth-links" style="margin-top: 20px;">
        Already have an account? <a href="index.php">Log In here</a>
    </div>

    <?php if ($registerSuccess): ?>
        <div class="success-box">
            <strong>Registration Successful!</strong><br>
            Welcome, <?= $fname ?> <?= $lname ?>. You can now go to the login page.
        </div>
    <?php endif; ?>
</div>

</body>
</html>