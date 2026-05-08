<?php $user = $_SESSION['user']; $isEdit = !empty($editing); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manager Dashboard &mdash; Cafe Management</title>
<link rel="stylesheet" href="style.css">
</head>
<body class="app-body">
<header class="navbar">
    <div class="navbar-inner">
        <a class="brand" href="index.php?page=manager"><span class="brand-icon">&#9749;</span><span>CafeSys</span></a>
        <div class="nav-user">
            <span class="user-pill">
                <span class="user-avatar"><?= strtoupper(substr($user['name'], 0, 1)) ?></span>
                <span class="user-meta">
                    <span class="user-name"><?= htmlspecialchars($user['name']) ?></span>
                    <span class="user-role">Manager</span>
                </span>
            </span>
            <a href="index.php?page=logout" class="btn-logout">Logout</a>
        </div>
    </div>
</header>
<main class="main-content">
    <div class="page-header">
        <div>
            <h1 class="page-title">Manage Menu</h1>
            <p class="page-sub">Add, edit, search and remove items in the cafe menu</p>
        </div>
    </div>
    <?php if (isset($_GET['msg'])): ?>
        <?php $messages = ['added' => 'Item added successfully.', 'updated' => 'Item updated successfully.', 'deleted' => 'Item deleted successfully.'];
              $msg = $messages[$_GET['msg']] ?? null; ?>
        <?php if ($msg): ?><div class="alert alert-success"><?= $msg ?></div><?php endif; ?>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <div class="card form-card">
        <h3 class="card-title"><?= $isEdit ? '&#9998; Edit Item (#' . intval($editing['id']) . ')' : '+ Add New Item' ?></h3>
        <form method="POST" action="index.php?page=manager&action=<?= $isEdit ? 'update&id=' . intval($editing['id']) : 'add' ?>" class="form" novalidate>
            <div class="field-row">
                <div class="field">
                    <label for="name">Item Name</label>
                    <input type="text" id="name" name="name" value="<?= htmlspecialchars($editing['name'] ?? '') ?>" placeholder="e.g. Caramel Macchiato" required>
                </div>
                <div class="field">
                    <label for="category">Category</label>
                    <input type="text" id="category" name="category" value="<?= htmlspecialchars($editing['category'] ?? '') ?>" placeholder="e.g. Hot Beverages" required>
                </div>
            </div>
            <div class="field-row">
                <div class="field">
                    <label for="quantity">Quantity (Stock)</label>
                    <input type="number" id="quantity" name="quantity" min="0" value="<?= htmlspecialchars($editing['quantity'] ?? '') ?>" placeholder="e.g. 50" required>
                </div>
                <div class="field">
                    <label for="price">Price ($)</label>
                    <input type="number" id="price" name="price" step="0.01" min="0" value="<?= htmlspecialchars($editing['price'] ?? '') ?>" placeholder="e.g. 4.50" required>
                </div>
            </div>
            <div class="form-actions">
                <?php if ($isEdit): ?>
                    <a href="index.php?page=manager" class="btn btn-ghost">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Item</button>
                <?php else: ?>
                    <button type="submit" class="btn btn-primary">Save Item</button>
                <?php endif; ?>
            </div>
        </form>
    </div>
    <div class="card">
        <div class="card-toolbar">
            <div class="search-wrap">
                <span class="search-icon">&#128269;</span>
                <input type="text" id="searchInput" class="search-input" placeholder="Search by name or category...">
            </div>
            <span class="badge" id="resultCount"><?= count($items) ?> total</span>
        </div>
        <div class="table-wrap">
            <table class="data-table">
                <thead><tr><th>#</th><th>Name</th><th>Category</th><th>Stock</th><th>Price</th><th class="text-right">Actions</th></tr></thead>
                <tbody id="tableBody">
                    <?php if (empty($items)): ?>
                        <tr><td colspan="6" class="empty">No items yet.</td></tr>
                    <?php else: foreach ($items as $i => $item): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= htmlspecialchars($item['name']) ?></td>
                            <td><?= htmlspecialchars($item['category']) ?></td>
                            <td><?= htmlspecialchars($item['quantity']) ?></td>
                            <td>$<?= number_format($item['price'], 2) ?></td>
                            <td class="text-right">
                                <a class="btn-sm btn-edit" href="index.php?page=manager&action=edit&id=<?= $item['id'] ?>">Edit</a>
                                <a class="btn-sm btn-delete" href="index.php?page=manager&action=delete&id=<?= $item['id'] ?>" onclick="return confirm('Delete this item?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>
<footer class="footer">&copy; <?= date('Y') ?> Cafe Management System</footer>
<script>
(function () {
    var input = document.getElementById('searchInput');
    var body = document.getElementById('tableBody');
    var counter = document.getElementById('resultCount');
    var timer;
    function esc(s) { return String(s == null ? '' : s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#039;'); }
    function render(rows) {
        if (!rows.length) { body.innerHTML = '<tr><td colspan="6" class="empty">No matching results.</td></tr>'; counter.textContent = '0 results'; return; }
        var html = '';
        rows.forEach(function (b, i) {
            html += '<tr><td>' + (i + 1) + '</td><td>' + esc(b.name) + '</td><td>' + esc(b.category) + '</td><td>' + esc(b.quantity) + '</td><td>$' + parseFloat(b.price).toFixed(2) + '</td><td class="text-right"><a class="btn-sm btn-edit" href="index.php?page=manager&action=edit&id=' + b.id + '">Edit</a> <a class="btn-sm btn-delete" href="index.php?page=manager&action=delete&id=' + b.id + '" onclick="return confirm(\'Delete this item?\')">Delete</a></td></tr>';
        });
        body.innerHTML = html; counter.textContent = rows.length + (input.value.trim() ? ' results' : ' total');
    }
    input.addEventListener('input', function () {
        clearTimeout(timer);
        timer = setTimeout(function () {
            fetch('index.php?page=ajax&type=item&q=' + encodeURIComponent(input.value.trim()), { credentials: 'same-origin' })
                .then(function (r) { return r.json(); }).then(render).catch(function (e) { console.error(e); });
        }, 200);
    });
})();
</script>
</body>
</html>