# InvenTrack — Inventory Management System

A clean, modern, admin-only Inventory Management System built with PHP, MySQL, and Bootstrap-style custom CSS.

---

## 📁 Project Structure

```
inventory/
├── frontend/
│   ├── index.html          ← Login page
│   ├── dashboard.php       ← Stats overview
│   ├── products.php        ← Product management
│   ├── categories.php      ← Category management
│   ├── sidebar.php         ← Shared sidebar (auto-included)
│   ├── style.css           ← All styles
│   └── uploads/            ← Product image uploads (auto-created)
├── backend/
│   ├── config.php          ← DB connection (PDO)
│   ├── auth_check.php      ← Session protection
│   ├── login.php           ← Login handler
│   ├── logout.php          ← Session destroy + redirect
│   ├── product_add.php     ← Add & Update products
│   ├── product_delete.php  ← Delete products
│   ├── product_update.php  ← Redirect shim
│   └── category_crud.php   ← Add / Edit / Delete categories
└── db/
    └── inventory_db.sql    ← Full DB schema + seed data
```

---

## ⚙️ Installation (XAMPP)

### Step 1 — Copy project
Place the `inventory/` folder inside:
```
C:\xampp\htdocs\inventory\
```

### Step 2 — Import database
1. Open **phpMyAdmin** → `http://localhost/phpmyadmin`
2. Click **Import** tab
3. Choose `db/inventory_db.sql`
4. Click **Go**

### Step 3 — Access the app
Open your browser:
```
http://localhost/inventory/frontend/index.html
```

### Step 4 — Login
| Field    | Value      |
|----------|------------|
| Username | `admin`    |
| Password | `admin123` |

---

## 🔐 Changing the Admin Password

Generate a new hash in PHP:
```php
echo password_hash('your_new_password', PASSWORD_DEFAULT);
```
Then update in the database:
```sql
UPDATE users SET password = 'new_hash_here' WHERE username = 'admin';
```

---

## ✅ Features

| Feature              | Status |
|----------------------|--------|
| Secure login (hashed)| ✅     |
| Session protection   | ✅     |
| Dashboard stats      | ✅     |
| Product CRUD         | ✅     |
| Category CRUD        | ✅     |
| Product image upload | ✅     |
| Search products      | ✅     |
| Pagination           | ✅     |
| Export to CSV        | ✅     |
| Low stock alerts     | ✅     |
| Responsive design    | ✅     |
| PDO prepared stmts   | ✅     |
| XSS protection       | ✅     |

---

## 🔒 Security Notes
- Passwords are hashed with `password_hash()` + `PASSWORD_DEFAULT`
- All DB queries use **PDO prepared statements** — no SQL injection
- All output is escaped with `htmlspecialchars()`
- Sessions are regenerated on login
- All protected pages require `auth_check.php`
