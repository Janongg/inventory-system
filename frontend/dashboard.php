<?php
require_once '../backend/auth_check.php';
require_once '../backend/config.php';

// Stats
$totalProducts = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$totalCategories = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
$totalQty = $pdo->query("SELECT SUM(quantity) FROM products")->fetchColumn() ?? 0;
$lowStock = $pdo->query("SELECT COUNT(*) FROM products WHERE quantity < 5")->fetchColumn();
$totalValue = $pdo->query("SELECT SUM(quantity * price) FROM products")->fetchColumn() ?? 0;

// Low stock items
$lowStockItems = $pdo->query("
    SELECT p.*, c.name AS category_name
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    WHERE p.quantity < 5
    ORDER BY p.quantity ASC
    LIMIT 10
")->fetchAll();

// Recent products
$recentProducts = $pdo->query("
    SELECT p.*, c.name AS category_name
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    ORDER BY p.created_at DESC
    LIMIT 8
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — InvenTrack</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="app-wrapper">

    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <!-- Topbar -->
        <header class="topbar">
            <div style="display:flex;align-items:center;gap:12px;">
                <button class="sidebar-toggle" onclick="toggleSidebar()">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                </button>
                <span class="topbar-title">Dashboard</span>
            </div>
            <div class="topbar-right">
                <span style="font-size:0.78rem;color:var(--text-muted);"><?= date('l, M j, Y') ?></span>
            </div>
        </header>

        <div class="page-body">
            <!-- Stats -->
            <div class="stats-grid">
                <div class="stat-card" style="--card-accent:var(--accent);--card-icon-bg:rgba(108,99,255,0.12);--card-icon-color:var(--accent2)">
                    <div class="stat-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                    </div>
                    <div class="stat-value"><?= number_format($totalProducts) ?></div>
                    <div class="stat-label">Total Products</div>
                </div>
                <div class="stat-card" style="--card-accent:var(--warning);--card-icon-bg:rgba(245,158,11,0.12);--card-icon-color:var(--warning)">
                    <div class="stat-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    </div>
                    <div class="stat-value"><?= number_format($lowStock) ?></div>
                    <div class="stat-label">Low Stock Items</div>
                </div>
                <div class="stat-card" style="--card-accent:var(--success);--card-icon-bg:rgba(16,185,129,0.12);--card-icon-color:var(--success)">
                    <div class="stat-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 20h16a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.93a2 2 0 0 1-1.66-.9l-.82-1.2A2 2 0 0 0 7.93 3H4a2 2 0 0 0-2 2v13c0 1.1.9 2 2 2z"/></svg>
                    </div>
                    <div class="stat-value"><?= number_format($totalCategories) ?></div>
                    <div class="stat-label">Categories</div>
                </div>
                <div class="stat-card" style="--card-accent:var(--info);--card-icon-bg:rgba(59,130,246,0.12);--card-icon-color:var(--info)">
                    <div class="stat-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                    </div>
                    <div class="stat-value">₱<?= number_format($totalValue, 0) ?></div>
                    <div class="stat-label">Total Inventory Value</div>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">

                <!-- Low Stock Alert -->
                <div class="card">
                    <div class="card-header">
                        <span class="card-title">⚠️ Low Stock Alert</span>
                        <a href="products.php" class="btn btn-ghost btn-sm">View All</a>
                    </div>
                    <div class="table-wrap">
                        <?php if (empty($lowStockItems)): ?>
                        <div class="empty-state">
                            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><polyline points="20 6 9 17 4 12"/></svg>
                            <p>All products are well-stocked!</p>
                        </div>
                        <?php else: ?>
                        <table>
                            <thead><tr><th>Product</th><th>Category</th><th>Qty</th></tr></thead>
                            <tbody>
                            <?php foreach ($lowStockItems as $item): ?>
                            <tr>
                                <td class="fw-600"><?= htmlspecialchars($item['name']) ?></td>
                                <td><span class="badge badge-purple"><?= htmlspecialchars($item['category_name'] ?? 'Uncategorized') ?></span></td>
                                <td>
                                    <?php if ($item['quantity'] == 0): ?>
                                    <span class="badge badge-danger">Out of Stock</span>
                                    <?php else: ?>
                                    <span class="badge badge-warning"><?= $item['quantity'] ?></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Recent Products -->
                <div class="card">
                    <div class="card-header">
                        <span class="card-title">🕒 Recently Added</span>
                        <a href="products.php" class="btn btn-ghost btn-sm">View All</a>
                    </div>
                    <div class="table-wrap">
                        <?php if (empty($recentProducts)): ?>
                        <div class="empty-state"><p>No products yet.</p></div>
                        <?php else: ?>
                        <table>
                            <thead><tr><th>Product</th><th>Price</th><th>Stock</th></tr></thead>
                            <tbody>
                            <?php foreach ($recentProducts as $p): ?>
                            <tr>
                                <td>
                                    <div style="font-weight:600;font-size:0.85rem;"><?= htmlspecialchars($p['name']) ?></div>
                                    <div style="font-size:0.75rem;color:var(--text-muted);"><?= htmlspecialchars($p['category_name'] ?? 'Uncategorized') ?></div>
                                </td>
                                <td>₱<?= number_format($p['price'], 2) ?></td>
                                <td>
                                    <?php
                                    $qty = $p['quantity'];
                                    if ($qty == 0) echo '<span class="badge badge-danger">0</span>';
                                    elseif ($qty < 5) echo '<span class="badge badge-warning">'.$qty.'</span>';
                                    else echo '<span class="badge badge-success">'.$qty.'</span>';
                                    ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('open');
}
</script>
</body>
</html>
