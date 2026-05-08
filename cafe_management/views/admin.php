<?php $user = $_SESSION['user']; $isEdit = !empty($editing); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard &mdash; Cafe Management</title>
<link rel="stylesheet" href="style.css">
</head>
<body class="app-body">
<header class="navbar">
    <div class="navbar-inner">
        <a class="brand" href="index.php?page=admin"><span class="brand-icon">&#9749;</span><span>CafeSys</span></a>
        <div class="nav-user">
            <span class="user-pill">
                <span class="user-avatar"><?= strtoupper(substr($user['name'], 0, 1)) ?></span>
                <span class="user-meta">
                    <span class="user-name"><?= htmlspecialchars($user['name']) ?></span>
                    <span class="user-role">Admin</span>
                </span>
            </span>
            <a href="index.php?page=logout" class="btn-logout">Logout</a>
        </div>
    </div>
</header>
<main class="main-content">
    <div class="page-header">
        <div>
            <h1 class="page-title">Manage Managers</h1>
            <p class="page-sub">Register, edit, search and remove manager accounts</p>
        </div>
    </div>
    <?php if (isset($_GET['msg'])): ?>
        <?php $messages = ['added' => 'Manager added successfully.', 'updated' => 'Manager updated successfully.', 'deleted' => 'Manager deleted successfully.'];
              $msg = $messages[$_GET['msg']] ?? null; ?>
        <?php if ($msg): ?><div class="alert alert-success"><?= $msg ?></div><?php endif; ?>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <div class="card form-card">
        <h3 class="card-title"><?= $isEdit ? '&#9998; Edit Manager (#' . intval($editing['id']) . ')' : '+ Add New Manager' ?></h3>
        <form method="POST" action="index.php?page=admin&action=<?= $isEdit ? 'update&id=' . intval($editing['id']) : 'add' ?>" class="form" novalidate>
            <div class="field-row">
                <div class="field">
                    <label for="name">Manager Name</label>
                    <input type="text" id="name" name="name" value="<?= htmlspecialchars($editing['name'] ?? '') ?>" placeholder="Full name" required>
                </div>
                <div class="field">
                    <label for="contact">Contact</label>
                    <input type="text" id="contact" name="contact" value="<?= htmlspecialchars($editing['contact'] ?? '') ?>" placeholder="e.g. +880 1XXXXXXXXX" required>
                </div>
            </div>
            <div class="field-row">
                <div class="field">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" value="<?= htmlspecialchars($editing['username'] ?? '') ?>" placeholder="Login username" required>
                </div>
                <?php if (!$isEdit): ?>
                <div class="field">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Min 6 characters" required>
                </div>
                <?php endif; ?>
            </div>
            <div class="form-actions">
                <?php if ($isEdit): ?>
                    <a href="index.php?page=admin" class="btn btn-ghost">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Manager</button>
                <?php else: ?>
                    <button type="submit" class="btn btn-primary">Save Manager</button>
                <?php endif; ?>
            </div>
        </form>
    </div>
    <div class="card">
        <div class="card-toolbar">
            <div class="search-wrap">
                <span class="search-icon">&#128269;</span>
                <input type="text" id="searchInput" class="search-input" placeholder="Search by name, username or contact...">
            </div>
            <span class="badge" id="resultCount"><?= count($managers) ?> total</span>
        </div>
        <div class="table-wrap">
            <table class="data-table">
                <thead><tr><th>#</th><th>Name</th><th>Contact</th><th>Username</th><th class="text-right">Actions</th></tr></thead>
                <tbody id="tableBody">
                    <?php if (empty($managers)): ?>
                        <tr><td colspan="5" class="empty">No managers yet.</td></tr>
                    <?php else: foreach ($managers as $i => $mgr): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= htmlspecialchars($mgr['name']) ?></td>
                            <td><?= htmlspecialchars($mgr['contact']) ?></td>
                            <td><?= htmlspecialchars($mgr['username']) ?></td>
                            <td class="text-right">
                                <a class="btn-sm btn-edit" href="index.php?page=admin&action=edit&id=<?= $mgr['id'] ?>">Edit</a>
                                <a class="btn-sm btn-delete" href="index.php?page=admin&action=delete&id=<?= $mgr['id'] ?>" onclick="return confirm('Delete this manager?')">Delete</a>
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
        if (!rows.length) { body.innerHTML = '<tr><td colspan="5" class="empty">No matching results.</td></tr>'; counter.textContent = '0 results'; return; }
        var html = '';
        rows.forEach(function (r, i) {
            html += '<tr><td>' + (i + 1) + '</td><td>' + esc(r.name) + '</td><td>' + esc(r.contact) + '</td><td>' + esc(r.username) + '</td><td class="text-right"><a class="btn-sm btn-edit" href="index.php?page=admin&action=edit&id=' + r.id + '">Edit</a> <a class="btn-sm btn-delete" href="index.php?page=admin&action=delete&id=' + r.id + '" onclick="return confirm(\'Delete this manager?\')">Delete</a></td></tr>';
        });
        body.innerHTML = html; counter.textContent = rows.length + (input.value.trim() ? ' results' : ' total');
    }
    input.addEventListener('input', function () {
        clearTimeout(timer);
        timer = setTimeout(function () {
            fetch('index.php?page=ajax&type=manager&q=' + encodeURIComponent(input.value.trim()), { credentials: 'same-origin' })
                .then(function (r) { return r.json(); }).then(render).catch(function (e) { console.error(e); });
        }, 200);
    });
})();
</script>
</body>
</html>