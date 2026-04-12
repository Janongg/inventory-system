<?php
// sidebar.php - include in all protected pages
$current = basename($_SERVER['PHP_SELF']);
?>
<!-- Sidebar -->
<nav class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon">
            <svg width="28" height="28" viewBox="0 0 28 28" fill="none">
                <rect x="2" y="2" width="11" height="11" rx="2" fill="var(--accent)"/>
                <rect x="15" y="2" width="11" height="11" rx="2" fill="var(--accent)" opacity="0.6"/>
                <rect x="2" y="15" width="11" height="11" rx="2" fill="var(--accent)" opacity="0.6"/>
                <rect x="15" y="15" width="11" height="11" rx="2" fill="var(--accent)" opacity="0.3"/>
            </svg>
        </div>
        <div class="brand-text">
            <span class="brand-name">InvenTrack</span>
            <span class="brand-sub">Admin Panel</span>
        </div>
    </div>

    <div class="sidebar-admin">
        <div class="admin-avatar"><?= strtoupper(substr($_SESSION['admin_username'], 0, 1)) ?></div>
        <div class="admin-info">
            <span class="admin-label">Logged in as</span>
            <span class="admin-name"><?= htmlspecialchars($_SESSION['admin_username']) ?></span>
        </div>
    </div>

    <ul class="sidebar-nav">
        <li class="nav-section">MAIN MENU</li>
        <li class="<?= $current === 'dashboard.php' ? 'active' : '' ?>">
            <a href="dashboard.php">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                Dashboard
            </a>
        </li>
        <li class="<?= $current === 'products.php' ? 'active' : '' ?>">
            <a href="products.php">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                Products
            </a>
        </li>
        <li class="<?= $current === 'categories.php' ? 'active' : '' ?>">
            <a href="categories.php">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 20h16a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.93a2 2 0 0 1-1.66-.9l-.82-1.2A2 2 0 0 0 7.93 3H4a2 2 0 0 0-2 2v13c0 1.1.9 2 2 2z"/></svg>
                Categories
            </a>
        </li>
        <li class="nav-section">ACCOUNT</li>
        <li>
            <a href="../backend/logout.php" class="logout-link">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16,17 21,12 16,7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                Logout
            </a>
        </li>
    </ul>
</nav>
