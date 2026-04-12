<?php
session_start();

// Redirect if already logged in
if (isset($_SESSION['admin'])) {
    header("Location: ../frontend/dashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../frontend/index.html");
    exit();
}

require_once 'config.php';

$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

$error = '';

if (empty($username) || empty($password)) {
    $error = 'Please enter both username and password.';
} else {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['admin'] = $user['id'];
        $_SESSION['admin_username'] = $user['username'];
        header("Location: ../frontend/dashboard.php");
        exit();
    } else {
        $error = 'Invalid username or password.';
    }
}

// Redirect back to login with error
header("Location: ../frontend/index.html?error=" . urlencode($error));
exit();
?>
