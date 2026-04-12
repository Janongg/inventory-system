<?php
require_once '../backend/auth_check.php';
require_once '../backend/config.php';

// Fetch categories with product count
$categories = $pdo->query("
    SELECT c.*, COUNT(p.id) AS product_count
    FROM categories c
    LEFT JOIN products p ON p.category_id = c.id
    GROUP BY c.id
    ORDER BY c.name
")->fetchAll();

$success = $_GET['success'] ?? '';
$error   = $_GET['error']   ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categories — InvenTrack</title>
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
                <span class="topbar-title">Categories</span>
            </div>
            <div class="topbar-right">
                <button class="btn btn-primary btn-sm" onclick="openModal('addModal')">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Add Category
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

            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px;margin-bottom:28px;">
                <?php foreach ($categories as $cat): ?>
                <div class="card" style="border-radius:var(--radius);overflow:hidden;">
                    <div style="padding:20px;">
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div style="width:38px;height:38px;background:rgba(108,99,255,0.12);border-radius:9px;display:flex;align-items:center;justify-content:center;color:var(--accent2);">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 20h16a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.93a2 2 0 0 1-1.66-.9l-.82-1.2A2 2 0 0 0 7.93 3H4a2 2 0 0 0-2 2v13c0 1.1.9 2 2 2z"/></svg>
                                </div>
                                <div>
                                    <div style="font-family:'Syne',sans-serif;font-weight:700;font-size:0.95rem;"><?= htmlspecialchars($cat['name']) ?></div>
                                    <div style="font-size:0.75rem;color:var(--text-muted);">ID #<?= $cat['id'] ?></div>
                                </div>
                            </div>
                            <span class="badge badge-purple"><?= $cat['product_count'] ?> items</span>
                        </div>
                        <div style="font-size:0.75rem;color:var(--text-muted);margin-bottom:16px;">
                            Created: <?= date('M j, Y', strtotime($cat['created_at'])) ?>
                        </div>
                        <div style="display:flex;gap:8px;">
                            <button class="btn btn-ghost btn-sm" style="flex:1;"
                                onclick='openEditModal(<?= json_encode(["id"=>$cat["id"],"name"=>$cat["name"]]) ?>)'>
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                Edit
                            </button>
                            <a href="../backend/category_crud.php?action=delete&id=<?= $cat['id'] ?>"
                               class="btn btn-danger btn-sm" style="flex:1;"
                               onclick="return confirm('Delete this category? Products in it will be uncategorized.')">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                                Delete
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>

                <?php if (empty($categories)): ?>
                <div class="card" style="grid-column:1/-1;">
                    <div class="empty-state">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4 20h16a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.93a2 2 0 0 1-1.66-.9l-.82-1.2A2 2 0 0 0 7.93 3H4a2 2 0 0 0-2 2v13c0 1.1.9 2 2 2z"/></svg>
                        <p>No categories yet. Create your first category!</p>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Category list table -->
            <?php if (!empty($categories)): ?>
            <div class="card">
                <div class="card-header">
                    <span class="card-title">All Categories</span>
                </div>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Category Name</th>
                                <th>Products</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($categories as $cat): ?>
                        <tr>
                            <td style="color:var(--text-muted);font-size:0.8rem;"><?= $cat['id'] ?></td>
                            <td class="fw-600"><?= htmlspecialchars($cat['name']) ?></td>
                            <td><span class="badge badge-info"><?= $cat['product_count'] ?></span></td>
                            <td style="color:var(--text-muted);font-size:0.82rem;"><?= date('M j, Y', strtotime($cat['created_at'])) ?></td>
                            <td>
                                <div style="display:flex;gap:6px;">
                                    <button class="btn btn-ghost btn-sm btn-icon"
                                        onclick='openEditModal(<?= json_encode(["id"=>$cat["id"],"name"=>$cat["name"]]) ?>)' title="Edit">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    </button>
                                    <a href="../backend/category_crud.php?action=delete&id=<?= $cat['id'] ?>"
                                       class="btn btn-danger btn-sm btn-icon" title="Delete"
                                       onclick="return confirm('Delete this category?')">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Add Category Modal -->
<div class="modal-overlay" id="addModal">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title">Add New Category</span>
            <button class="btn-close-modal" onclick="closeModal('addModal')">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <form action="../backend/category_crud.php" method="POST">
            <input type="hidden" name="action" value="add">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Category Name *</label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. Electronics, Furniture..." required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="closeModal('addModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Add Category</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Category Modal -->
<div class="modal-overlay" id="editModal">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title">Edit Category</span>
            <button class="btn-close-modal" onclick="closeModal('editModal')">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <form action="../backend/category_crud.php" method="POST">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" id="edit_id">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Category Name *</label>
                    <input type="text" name="name" id="edit_name" class="form-control" required>
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

function openEditModal(cat) {
    document.getElementById('edit_id').value   = cat.id;
    document.getElementById('edit_name').value = cat.name;
    openModal('editModal');
}

setTimeout(() => {
    document.querySelectorAll('.alert').forEach(a => a.style.opacity = '0');
}, 4000);
</script>
</body>
</html>
