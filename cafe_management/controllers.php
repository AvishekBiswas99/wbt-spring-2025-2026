<?php
/* ============== Login ============== */
function loginCtrl($conn) {
    $error = '';
    $prefill = $_COOKIE['remember_user'] ?? '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $u = trim($_POST['username'] ?? '');
        $p = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);

        if ($u === '' || $p === '') {
            $error = 'Please fill in both fields.';
        } else {
            $admin = authAdmin($conn, $u, $p);
            if ($admin) {
                $_SESSION['user'] = ['id' => $admin['id'], 'username' => $admin['username'], 'name' => 'Administrator', 'role' => 'admin'];
                if ($remember) setcookie('remember_user', $u, time() + 86400 * 30, '/');
                else setcookie('remember_user', '', time() - 3600, '/');
                header('Location: index.php?page=admin');
                exit;
            }
            $mgr = authManager($conn, $u, $p);
            if ($mgr) {
                $_SESSION['user'] = ['id' => $mgr['id'], 'username' => $mgr['username'], 'name' => $mgr['name'], 'role' => 'manager'];
                if ($remember) setcookie('remember_user', $u, time() + 86400 * 30, '/');
                else setcookie('remember_user', '', time() - 3600, '/');
                header('Location: index.php?page=manager');
                exit;
            }
            $error = 'Invalid username or password.';
        }
    }
    require 'views/login.php';
}

/* ============== Register ============== */
function registerCtrl($conn) {
    $error = $success = '';
    $old = ['name' => '', 'contact' => '', 'username' => ''];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name     = trim($_POST['name'] ?? '');
        $contact  = trim($_POST['contact'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['confirm_password'] ?? '';
        $old = compact('name', 'contact', 'username');

        if ($name === '' || $contact === '' || $username === '' || $password === '') {
            $error = 'All fields are required.';
        } elseif (strlen($password) < 6) {
            $error = 'Password must be at least 6 characters.';
        } elseif ($password !== $confirm) {
            $error = 'Passwords do not match.';
        } elseif (managerUsernameExists($conn, $username)) {
            $error = 'Username is already taken.';
        } else {
            if (addManager($conn, $name, $contact, $username, $password)) {
                $success = 'Account created! You can now log in.';
                $old = ['name' => '', 'contact' => '', 'username' => ''];
            } else {
                $error = 'Registration failed. Try again.';
            }
        }
    }
    require 'views/register.php';
}

/* ============== Admin Dashboard ============== */
function adminCtrl($conn) {
    $action = $_GET['action'] ?? 'list';
    $error = '';
    $editing = null;

    if ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = trim($_POST['name'] ?? ''); $contact = trim($_POST['contact'] ?? '');
        $username = trim($_POST['username'] ?? ''); $password = $_POST['password'] ?? '';

        if ($name === '' || $contact === '' || $username === '' || $password === '') {
            $error = 'All fields are required.';
        } elseif (strlen($password) < 6) {
            $error = 'Password must be at least 6 characters.';
        } elseif (managerUsernameExists($conn, $username)) {
            $error = 'Username is already taken.';
        } else {
            if (addManager($conn, $name, $contact, $username, $password)) {
                header('Location: index.php?page=admin&msg=added'); exit;
            }
            $error = 'Failed to add manager.';
        }
    }

    if ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = intval($_GET['id'] ?? 0);
        $name = trim($_POST['name'] ?? ''); $contact = trim($_POST['contact'] ?? '');
        $username = trim($_POST['username'] ?? '');

        if ($name === '' || $contact === '' || $username === '') {
            $error = 'No field can be empty (NULL). All fields are required.';
            $editing = compact('id', 'name', 'contact', 'username');
        } elseif (managerUsernameExists($conn, $username, $id)) {
            $error = 'That username is used by another manager.';
            $editing = compact('id', 'name', 'contact', 'username');
        } else {
            if (updateManager($conn, $id, $name, $contact, $username)) {
                header('Location: index.php?page=admin&msg=updated'); exit;
            }
            $error = 'Update failed.';
            $editing = compact('id', 'name', 'contact', 'username');
        }
    }

    if ($action === 'edit' && !$editing) {
        $id = intval($_GET['id'] ?? 0);
        $editing = getManager($conn, $id);
    }

    if ($action === 'delete') {
        $id = intval($_GET['id'] ?? 0);
        if ($id > 0) deleteManager($conn, $id);
        header('Location: index.php?page=admin&msg=deleted'); exit;
    }

    $managers = getManagers($conn);
    require 'views/admin.php';
}

/* ============== Manager Dashboard ============== */
function managerCtrl($conn) {
    $action = $_GET['action'] ?? 'list';
    $error = '';
    $editing = null;

    if ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = trim($_POST['name'] ?? ''); $category = trim($_POST['category'] ?? '');
        $quantity = trim($_POST['quantity'] ?? ''); $price = trim($_POST['price'] ?? '');

        if ($name === '' || $category === '' || $quantity === '' || $price === '') {
            $error = 'All fields are required.';
        } elseif (!ctype_digit($quantity) || intval($quantity) < 0) {
            $error = 'Quantity must be a non-negative whole number.';
        } elseif (!is_numeric($price) || floatval($price) < 0) {
            $error = 'Price must be a non-negative number.';
        } else {
            $mgrId = $_SESSION['user']['id'];
            if (addItem($conn, $name, $category, intval($quantity), floatval($price), $mgrId)) {
                header('Location: index.php?page=manager&msg=added'); exit;
            }
            $error = 'Failed to add item.';
        }
    }

    if ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = intval($_GET['id'] ?? 0);
        $name = trim($_POST['name'] ?? ''); $category = trim($_POST['category'] ?? '');
        $quantity = trim($_POST['quantity'] ?? ''); $price = trim($_POST['price'] ?? '');

        if ($name === '' || $category === '' || $quantity === '' || $price === '') {
            $error = 'No field can be empty (NULL). All fields are required.';
            $editing = compact('id', 'name', 'category', 'quantity', 'price');
        } elseif (!ctype_digit($quantity) || intval($quantity) < 0) {
            $error = 'Quantity must be a non-negative whole number.';
            $editing = compact('id', 'name', 'category', 'quantity', 'price');
        } elseif (!is_numeric($price) || floatval($price) < 0) {
            $error = 'Price must be a non-negative number.';
            $editing = compact('id', 'name', 'category', 'quantity', 'price');
        } else {
            if (updateItem($conn, $id, $name, $category, intval($quantity), floatval($price))) {
                header('Location: index.php?page=manager&msg=updated'); exit;
            }
            $error = 'Update failed.';
            $editing = compact('id', 'name', 'category', 'quantity', 'price');
        }
    }

    if ($action === 'edit' && !$editing) {
        $id = intval($_GET['id'] ?? 0);
        $editing = getItem($conn, $id);
    }

    if ($action === 'delete') {
        $id = intval($_GET['id'] ?? 0);
        if ($id > 0) deleteItem($conn, $id);
        header('Location: index.php?page=manager&msg=deleted'); exit;
    }

    $items = getItems($conn);
    require 'views/manager.php';
}
?>