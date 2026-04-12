<?php
require_once 'auth_check.php';
require_once 'config.php';

$action = $_POST['action'] ?? 'add';
$redirect = "../frontend/products.php";

function sanitize($val) {
    return htmlspecialchars(strip_tags(trim($val)));
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: $redirect");
    exit();
}

$name        = sanitize($_POST['name'] ?? '');
$category_id = intval($_POST['category_id'] ?? 0);
$quantity    = intval($_POST['quantity'] ?? 0);
$price       = floatval($_POST['price'] ?? 0);
$date_added  = $_POST['date_added'] ?? date('Y-m-d');

// Validate date
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_added)) {
    $date_added = date('Y-m-d');
}

if (empty($name)) {
    header("Location: $redirect?error=" . urlencode("Product name is required."));
    exit();
}
if ($quantity < 0) {
    header("Location: $redirect?error=" . urlencode("Quantity cannot be negative."));
    exit();
}
if ($price < 0) {
    header("Location: $redirect?error=" . urlencode("Price cannot be negative."));
    exit();
}

// Handle image upload
$image = null;
if (!empty($_FILES['image']['name'])) {
    $uploadDir = '../frontend/uploads/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
    $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (in_array($ext, $allowed) && $_FILES['image']['size'] < 5000000) {
        $filename = uniqid('prod_') . '.' . $ext;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $filename)) {
            $image = $filename;
        }
    }
}

if ($action === 'add') {
    $stmt = $pdo->prepare(
        "INSERT INTO products (name, category_id, quantity, price, image, date_added)
         VALUES (?, ?, ?, ?, ?, ?)"
    );
    $stmt->execute([$name, $category_id ?: null, $quantity, $price, $image, $date_added]);
    header("Location: $redirect?success=" . urlencode("Product added successfully."));
} elseif ($action === 'update') {
    $id = intval($_POST['id'] ?? 0);
    if ($image) {
        $stmt = $pdo->prepare(
            "UPDATE products SET name=?, category_id=?, quantity=?, price=?, image=?, date_added=? WHERE id=?"
        );
        $stmt->execute([$name, $category_id ?: null, $quantity, $price, $image, $date_added, $id]);
    } else {
        $stmt = $pdo->prepare(
            "UPDATE products SET name=?, category_id=?, quantity=?, price=?, date_added=? WHERE id=?"
        );
        $stmt->execute([$name, $category_id ?: null, $quantity, $price, $date_added, $id]);
    }
    header("Location: $redirect?success=" . urlencode("Product updated successfully."));
}
exit();
?>
