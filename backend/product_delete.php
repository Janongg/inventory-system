<?php
require_once 'auth_check.php';
require_once 'config.php';

$id = intval($_GET['id'] ?? 0);

if ($id > 0) {
    // Delete image file if exists
    $stmt = $pdo->prepare("SELECT image FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch();
    if ($product && $product['image']) {
        $imgPath = '../frontend/uploads/' . $product['image'];
        if (file_exists($imgPath)) unlink($imgPath);
    }

    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: ../frontend/products.php?success=" . urlencode("Product deleted."));
} else {
    header("Location: ../frontend/products.php?error=" . urlencode("Invalid product ID."));
}
exit();
?>
