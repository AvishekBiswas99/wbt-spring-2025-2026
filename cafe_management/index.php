<?php
session_start();
require 'config.php';
require 'models.php';
require 'controllers.php';

$page = $_GET['page'] ?? 'login';

if ($page === 'logout') {
    $_SESSION = [];
    session_destroy();
    setcookie('remember_user', '', time() - 3600, '/');
    header('Location: index.php?page=login');
    exit;
}

if ($page === 'ajax') {
    header('Content-Type: application/json');
    if (!isset($_SESSION['user'])) {
        http_response_code(403);
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }
    $type = $_GET['type'] ?? '';
    $q    = trim($_GET['q'] ?? '');

    if ($type === 'manager' && $_SESSION['user']['role'] === 'admin') {
        echo json_encode($q === '' ? getManagers($conn) : searchManagers($conn, $q));
    } elseif ($type === 'item' && $_SESSION['user']['role'] === 'manager') {
        echo json_encode($q === '' ? getItems($conn) : searchItems($conn, $q));
    } else {
        http_response_code(403);
        echo json_encode(['error' => 'Forbidden']);
    }
    exit;
}

$publicPages = ['login', 'register'];

if (in_array($page, $publicPages) && isset($_SESSION['user'])) {
    header('Location: index.php?page=' . $_SESSION['user']['role']);
    exit;
}

if (!in_array($page, $publicPages) && !isset($_SESSION['user'])) {
    header('Location: index.php?page=login');
    exit;
}

if ($page === 'admin' && $_SESSION['user']['role'] !== 'admin') { header('Location: index.php?page=login'); exit; }
if ($page === 'manager' && $_SESSION['user']['role'] !== 'manager') { header('Location: index.php?page=login'); exit; }

switch ($page) {
    case 'login':     loginCtrl($conn);     break;
    case 'register':  registerCtrl($conn);  break;
    case 'admin':     adminCtrl($conn);     break;
    case 'manager':   managerCtrl($conn);   break;
    default: header('Location: index.php?page=login'); exit;
}
mysqli_close($conn);
?>