<?php
require_once '../backend/auth_check.php';
require_once '../backend/config.php';

// Search & Pagination
$search   = trim($_GET['search'] ?? '');
$page     = max(1, intval($_GET['page'] ?? 1));
$perPage  = 10;
$offset   = ($page - 1) * $perPage;

// Build query
$where  = '';
$params = [];
if ($search !== '') {
    $where  = "WHERE p.name LIKE ?";
    $params = ["%$search%"];
}

$totalCount = $pdo->prepare("SELECT COUNT(*) FROM products p $where");
$totalCount->execute($params);
$total = $totalCount->fetchColumn();
$pages = ceil($total / $perPage);

$stmt = $pdo->prepare("
    SELECT p.*, c.name AS category_name
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    $where
    ORDER BY p.created_at DESC
    LIMIT $perPage OFFSET $offset
");
$stmt->execute($params);
$products = $stmt->fetchAll();

// Categories for dropdown
$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();

// Edit product pre-fill
$editProduct = null;
if (isset($_GET['edit'])) {
    $s = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $s->execute([intval($_GET['edit'])]);
    $editProduct = $s->fetch();
}

// Messages
$success = $_GET['success'] ?? '';
$error   = $_GET['error']   ?? '';

// Export CSV
if (isset($_GET['export'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename=products_export.csv');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['ID', 'Name', 'Category', 'Quantity', 'Price', 'Date Added']);
    $all = $pdo->query("SELECT p.*, c.name AS cat FROM products p LEFT JOIN categories c ON p.category_id=c.id ORDER BY p.id")->fetchAll();
    foreach ($all as $r) {
        fputcsv($out, [$r['id'], $r['name'], $r['cat'] ?? 'Uncategorized', $r['quantity'], $r['price'], $r['date_added']]);
    }
    fclose($out);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products — InvenTrack</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="app-wrapper">

    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <header class="topbar">
            <div style="display:flex;align-items:center;gap:12px;">
                <button class="sidebar-toggle" onclick="toggleSidebar()">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                </button>
                <span class="topbar-title">Products</span>
            </div>
            <div class="topbar-right">
                <a href="?export=1<?= $search ? '&search='.urlencode($search) : '' ?>" class="btn btn-ghost btn-sm">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    Export CSV
                </a>
                <button class="btn btn-primary btn-sm" onclick="openModal('addModal')">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Add Product
                </button>
            </div>
        </header>

        <div class="page-body">
            <?php if ($success): ?>
            <div class="alert alert-success">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                <?= htmlspecialchars($success) ?>
            </div>
            <?php endif; ?>
            <?php if ($error): ?>
            <div class="alert alert-danger">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                <?= htmlspecialchars($error) ?>
            </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <span class="card-title">All Products <span style="color:var(--text-muted);font-size:0.8rem;font-weight:400;">(<?= $total ?>)</span></span>
                    <form method="GET" style="display:flex;gap:8px;align-items:center;">
                        <div class="search-box">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                            <input type="text" name="search" class="form-control" placeholder="Search products..." value="<?= htmlspecialchars($search) ?>" style="width:220px;">
                        </div>
                        <button type="submit" class="btn btn-ghost btn-sm">Search</button>
                        <?php if ($search): ?>
                        <a href="products.php" class="btn btn-ghost btn-sm">Clear</a>
                        <?php endif; ?>
                    </form>
                </div>
                <div class="table-wrap">
                    <?php if (empty($products)): ?>
                    <div class="empty-state">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                        <p><?= $search ? 'No products match your search.' : 'No products yet. Add your first product!' ?></p>
                    </div>
                    <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Img</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Date Added</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($products as $p): ?>
                        <tr>
                            <td>
                                <?php if ($p['image'] && file_exists('uploads/' . $p['image'])): ?>
                                <img src="uploads/<?= htmlspecialchars($p['image']) ?>" class="prod-img" alt="<?= htmlspecialchars($p['name']) ?>">
                                <?php else: ?>
                                <div class="prod-img-placeholder">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                </div>
                                <?php endif; ?>
                            </td>
                            <td class="fw-600"><?= htmlspecialchars($p['name']) ?></td>
                            <td><span class="badge badge-purple"><?= htmlspecialchars($p['category_name'] ?? 'Uncategorized') ?></span></td>
                            <td>
                                <?php
                                $qty = $p['quantity'];
                                if ($qty == 0) echo '<span class="badge badge-danger">Out of Stock</span>';
                                elseif ($qty < 5) echo '<span class="badge badge-warning">'.$qty.' low</span>';
                                else echo '<span class="badge badge-success">'.$qty.'</span>';
                                ?>
                            </td>
                            <td>₱<?= number_format($p['price'], 2) ?></td>
                            <td style="color:var(--text-muted);font-size:0.82rem;"><?= htmlspecialchars($p['date_added']) ?></td>
                            <td>
                                <div style="display:flex;gap:6px;">
                                    <button class="btn btn-ghost btn-sm btn-icon" title="Edit"
                                        onclick='openEditModal(<?= json_encode($p) ?>)'>
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    </button>
                                    <a href="../backend/product_delete.php?id=<?= $p['id'] ?>"
                                       class="btn btn-danger btn-sm btn-icon"
                                       title="Delete"
                                       onclick="return confirm('Delete this product?')">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>
                </div>

                <?php if ($pages > 1): ?>
                <div style="padding:16px 18px;border-top:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;">
                    <span style="font-size:0.78rem;color:var(--text-muted);">
                        Showing <?= ($offset + 1) ?>–<?= min($offset + $perPage, $total) ?> of <?= $total ?>
                    </span>
                    <div class="pagination">
                        <a href="?page=<?= $page-1 ?>&search=<?= urlencode($search) ?>" class="page-btn <?= $page <= 1 ? 'disabled' : '' ?>">‹</a>
                        <?php for ($i = max(1,$page-2); $i <= min($pages,$page+2); $i++): ?>
                        <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>" class="page-btn <?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
                        <?php endfor; ?>
                        <a href="?page=<?= $page+1 ?>&search=<?= urlencode($search) ?>" class="page-btn <?= $page >= $pages ? 'disabled' : '' ?>">›</a>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Add Product Modal -->
<div class="modal-overlay" id="addModal">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title">Add New Product</span>
            <button class="btn-close-modal" onclick="closeModal('addModal')">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <form action="../backend/product_add.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="add">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Product Name *</label>
                    <input type="text" name="name" class="form-control" placeholder="Enter product name" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Category</label>
                        <select name="category_id" class="form-select">
                            <option value="">— Select Category —</option>
                            <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Date Added</label>
                        <input type="date" name="date_added" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Quantity *</label>
                        <input type="number" name="quantity" class="form-control" placeholder="0" min="0" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Price (₱) *</label>
                        <input type="number" name="price" class="form-control" placeholder="0.00" min="0" step="0.01" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Product Image (optional)</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="closeModal('addModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Add Product</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Product Modal -->
<div class="modal-overlay" id="editModal">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title">Edit Product</span>
            <button class="btn-close-modal" onclick="closeModal('editModal')">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <form action="../backend/product_add.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" id="edit_id">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Product Name *</label>
                    <input type="text" name="name" id="edit_name" class="form-control" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Category</label>
                        <select name="category_id" id="edit_category" class="form-select">
                            <option value="">— Select Category —</option>
                            <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Date Added</label>
                        <input type="date" name="date_added" id="edit_date" class="form-control" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Quantity *</label>
                        <input type="number" name="quantity" id="edit_quantity" class="form-control" min="0" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Price (₱) *</label>
                        <input type="number" name="price" id="edit_price" class="form-control" min="0" step="0.01" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Replace Image (optional)</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="closeModal('editModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(id) { document.getElementById(id).classList.add('active'); }
function closeModal(id) { document.getElementById(id).classList.remove('active'); }
function toggleSidebar() { document.getElementById('sidebar').classList.toggle('open'); }

document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', function(e) {
        if (e.target === this) this.classList.remove('active');
    });
});

function openEditModal(product) {
    document.getElementById('edit_id').value       = product.id;
    document.getElementById('edit_name').value     = product.name;
    document.getElementById('edit_quantity').value = product.quantity;
    document.getElementById('edit_price').value    = product.price;
    document.getElementById('edit_date').value     = product.date_added;
    const sel = document.getElementById('edit_category');
    sel.value = product.category_id || '';
    openModal('editModal');
}

// Auto-dismiss alerts
setTimeout(() => {
    document.querySelectorAll('.alert').forEach(a => a.style.opacity = '0');
}, 4000);
</script>
</body>
</html>
