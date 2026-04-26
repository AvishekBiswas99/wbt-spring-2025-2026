<?php
$fnameErr = $lnameErr = $genderErr = $emailErr = $reasonErr = "";
$fname = $lname = $gender = $email = $company = $reason = $date = "";
$topics = [];

function cleanInput($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["firstname"])) {
        $fnameErr = "First name is required";
    } else {
        $fname = cleanInput($_POST["firstname"]);
        if (!preg_match("/^[a-zA-Z-' ]*$/", $fname)) {
            $fnameErr = "Only letters and white space allowed";
        }
    }

    if (empty($_POST["lastname"])) {
        $lnameErr = "Last name is required";
    } else {
        $lname = cleanInput($_POST["lastname"]);
        if (!preg_match("/^[a-zA-Z-' ]*$/", $lname)) {
            $lnameErr = "Only letters and white space allowed";
        }
    }

    if (empty($_POST["gender"])) {
        $genderErr = "Gender is required";
    } else {
        $gender = cleanInput($_POST["gender"]);
    }

    if (empty($_POST["email"])) {
        $emailErr = "Email is required";
    } else {
        $email = cleanInput($_POST["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailErr = "Invalid email format";
        }
    }

    $company = cleanInput($_POST["company"] ?? "");

    if (empty($_POST["reason"])) {
        $reasonErr = "Reason of contact is required";
    } else {
        $reason = cleanInput($_POST["reason"]);
    }

    if (!empty($_POST["topics"])) {
        foreach ($_POST["topics"] as $topic) {
            $topics[] = cleanInput($topic);
        }
    }

    $date = cleanInput($_POST["consultation_date"] ?? "");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact | Avishek</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .error, .required-note {
            color: #e74c3c;
            font-size: 13px;
            margin-left: 5px;
        }
        
        .contact-form {
            background-color: #fafafa;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 30px;
            margin-top: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            margin-bottom: 20px;
        }

        .form-group label {
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 8px;
        }

        .form-group input[type="text"],
        .form-group input[type="date"] {
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
            transition: border-color 0.3s;
        }

        .form-group input[type="text"]:focus,
        .form-group input[type="date"]:focus {
            border-color: #3498db;
            outline: none;
        }

        fieldset.form-group {
            padding: 15px 20px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        fieldset.form-group legend {
            font-weight: bold;
            color: #2c3e50;
            padding: 0 10px;
        }

        .radio-item, .checkbox-item {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
            color: #333;
        }

        .radio-item input, .checkbox-item input {
            transform: scale(1.2);
            cursor: pointer;
        }

        .radio-item label, .checkbox-item label {
            font-weight: normal;
            margin-bottom: 0;
            cursor: pointer;
        }

        .button-group {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }

        .btn-submit, .btn-reset {
            padding: 14px 20px;
            border: none;
            border-radius: 6px;
            color: white;
            font-weight: bold;
            font-size: 16px;
            cursor: pointer;
            flex: 1;
            transition: background-color 0.3s;
        }

        .btn-submit {
            background-color: #2c3e50;
        }

        .btn-submit:hover {
            background-color: #1a252f;
        }

        .btn-reset {
            background-color: #e74c3c;
        }

        .btn-reset:hover {
            background-color: #c0392b;
        }

        .results-container {
            margin-top: 40px;
            background-color: #e8f4f8;
            padding: 25px;
            border-radius: 8px;
            border: 1px solid #bce8f1;
        }

        .results-table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 15px;
            background-color: white;
        }

        .results-table th, .results-table td {
            padding: 12px 15px;
            border: 1px solid #ddd;
            text-align: left;
        }

        .results-table th {
            font-weight: bold;
            background-color: #2c3e50;
            color: white;
            width: 35%;
        }
    </style>
</head>
<body>

    <header>
        <nav>
            <ul>
                <li><a href="../index.php">Home</a></li>
                <li><a href="educations.php">Education</a></li>
                <li><a href="experience.php">Experience</a></li>
                <li><a href="projects.php">Projects</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section id="contact">
            <h2>Contact Me</h2>
            <p>Please fill out the form below to get in touch regarding projects, thesis collaborations, or job opportunities.</p>
            <p class="required-note">* required field</p>
            
            <form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" novalidate class="contact-form">
                
                <div class="form-group">
                    <label for="fname">First Name <span class="error">* <?= $fnameErr ?></span></label>
                    <input type="text" id="fname" name="firstname" value="<?= $fname ?>">
                </div>

                <div class="form-group">
                    <label for="lname">Last Name <span class="error">* <?= $lnameErr ?></span></label>
                    <input type="text" id="lname" name="lastname" value="<?= $lname ?>">
                </div>

                <fieldset class="form-group">
                    <legend>Gender <span class="error">* <?= $genderErr ?></span></legend>
                    <div class="radio-item">
                        <input type="radio" id="male" name="gender" value="Male" <?= ($gender == "Male") ? "checked" : "" ?>>
                        <label for="male">Male</label>
                    </div>
                    <div class="radio-item">
                        <input type="radio" id="female" name="gender" value="Female" <?= ($gender == "Female") ? "checked" : "" ?>>
                        <label for="female">Female</label>
                    </div>
                    <div class="radio-item">
                        <input type="radio" id="others" name="gender" value="Others" <?= ($gender == "Others") ? "checked" : "" ?>>
                        <label for="others">Others</label>
                    </div>
                </fieldset>

                <div class="form-group">
                    <label for="email">Email <span class="error">* <?= $emailErr ?></span></label>
                    <input type="text" id="email" name="email" value="<?= $email ?>">
                </div>

                <div class="form-group">
                    <label for="company">Company</label>
                    <input type="text" id="company" name="company" value="<?= $company ?>">
                </div>

                <fieldset class="form-group">
                    <legend>Reason of Contact <span class="error">* <?= $reasonErr ?></span></legend>
                    <div class="radio-item">
                        <input type="radio" id="reason-projects" name="reason" value="Projects" <?= ($reason == "Projects") ? "checked" : "" ?>>
                        <label for="reason-projects">Projects</label>
                    </div>
                    <div class="radio-item">
                        <input type="radio" id="reason-thesis" name="reason" value="Thesis" <?= ($reason == "Thesis") ? "checked" : "" ?>>
                        <label for="reason-thesis">Thesis</label>
                    </div>
                    <div class="radio-item">
                        <input type="radio" id="reason-job" name="reason" value="Job" <?= ($reason == "Job") ? "checked" : "" ?>>
                        <label for="reason-job">Job</label>
                    </div>
                </fieldset>

                <fieldset class="form-group">
                    <legend>Topics</legend>
                    <div class="checkbox-item">
                        <input type="checkbox" id="topic-web" name="topics[]" value="Web Development" <?= in_array("Web Development", $topics) ? "checked" : "" ?>>
                        <label for="topic-web">Web Development</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" id="topic-mobile" name="topics[]" value="Mobile Development" <?= in_array("Mobile Development", $topics) ? "checked" : "" ?>>
                        <label for="topic-mobile">Mobile Development</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" id="topic-ai" name="topics[]" value="AI/ML Development" <?= in_array("AI/ML Development", $topics) ? "checked" : "" ?>>
                        <label for="topic-ai">AI/ML Development</label>
                    </div>
                </fieldset>

                <div class="form-group">
                    <label for="date">Consultation Date</label>
                    <input type="date" id="date" name="consultation_date" value="<?= $date ?>">
                </div>

                <div class="button-group">
                    <button type="submit" class="btn-submit">Submit</button>
                    <button type="button" class="btn-reset" onclick="window.location.href='<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>'">Reset</button>
                </div>

            </form>

            <?php 
            if ($_SERVER["REQUEST_METHOD"] == "POST" &&
                !$fnameErr && !$lnameErr && !$emailErr && !$genderErr && !$reasonErr): ?>
                
                <div class="results-container">
                    <h3 style="color: #2c3e50; margin-bottom: 10px;">Submitted Values</h3>
                    <table class="results-table">
                        <tr><th>First Name</th><td><?= $fname ?></td></tr>
                        <tr><th>Last Name</th><td><?= $lname ?></td></tr>
                        <tr><th>Gender</th><td><?= $gender ?></td></tr>
                        <tr><th>Email</th><td><?= $email ?></td></tr>
                        <tr><th>Company</th><td><?= $company ?: "N/A" ?></td></tr>
                        <tr><th>Reason</th><td><?= $reason ?></td></tr>
                        <tr><th>Topics</th><td><?= empty($topics) ? "None" : implode(", ", $topics) ?></td></tr>
                        <tr><th>Consultation Date</th><td><?= $date ?: "Not specified" ?></td></tr>
                    </table>
                </div>
            <?php endif; ?>

        </section>
    </main>

    <footer>
        <div class="social-links">
            <a href="https://github.com/AvishekBiswas99" target="_blank">
                <img src="../images/Git.png" alt="GitHub" class="social-icon">
            </a>
            <a href="https://www.linkedin.com/in/avishek-biswas-ab5t4/" target="_blank">
                <img src="../images/in.png" alt="LinkedIn" class="social-icon">
            </a>
        </div>
        <p>&copy; 2026 Avishek. All rights reserved.</p>
    </footer>

</body>
</html>