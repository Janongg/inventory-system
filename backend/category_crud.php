<?php
require_once 'auth_check.php';
require_once 'config.php';

$action   = $_POST['action'] ?? ($_GET['action'] ?? '');
$redirect = "../frontend/categories.php";

if ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars(strip_tags(trim($_POST['name'] ?? '')));
    if (empty($name)) {
        header("Location: $redirect?error=" . urlencode("Category name is required."));
        exit();
    }
    // Check duplicate
    $stmt = $pdo->prepare("SELECT id FROM categories WHERE name = ?");
    $stmt->execute([$name]);
    if ($stmt->fetch()) {
        header("Location: $redirect?error=" . urlencode("Category already exists."));
        exit();
    }
    $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
    $stmt->execute([$name]);
    header("Location: $redirect?success=" . urlencode("Category added."));

} elseif ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id   = intval($_POST['id'] ?? 0);
    $name = htmlspecialchars(strip_tags(trim($_POST['name'] ?? '')));
    if (empty($name) || $id < 1) {
        header("Location: $redirect?error=" . urlencode("Invalid data."));
        exit();
    }
    $stmt = $pdo->prepare("UPDATE categories SET name = ? WHERE id = ?");
    $stmt->execute([$name, $id]);
    header("Location: $redirect?success=" . urlencode("Category updated."));

} elseif ($action === 'delete') {
    $id = intval($_GET['id'] ?? 0);
    if ($id < 1) {
        header("Location: $redirect?error=" . urlencode("Invalid ID."));
        exit();
    }
    // Nullify category_id in products
    $stmt = $pdo->prepare("UPDATE products SET category_id = NULL WHERE category_id = ?");
    $stmt->execute([$id]);
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: $redirect?success=" . urlencode("Category deleted."));

} else {
    header("Location: $redirect");
}
exit();
?>
