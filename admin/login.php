<?php
declare(strict_types=1);
require_once __DIR__ . '/config.php';
session_start();

if (!empty($_SESSION['admin_authenticated'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (hash_equals(ADMIN_PASSWORD, (string) ($_POST['password'] ?? ''))) {
        session_regenerate_id(true);
        $_SESSION['admin_authenticated'] = true;
        header('Location: dashboard.php');
        exit;
    }
    $error = 'Incorrect password.';
}
?>
<!doctype html>
<html lang="en">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Admin Login | Power Solutions</title><link rel="stylesheet" href="admin.css"></head>
<body class="admin-auth">
<main class="auth-card">
    <span class="eyebrow">POWER SOLUTIONS</span>
    <h1>Admin dashboard</h1>
    <p>Sign in to manage your storefront.</p>
    <?php if ($error): ?><div class="alert error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
    <form method="post">
        <label for="password">Password</label>
        <input id="password" type="password" name="password" required autofocus>
        <button class="button primary" type="submit">Sign in</button>
    </form>
    <small>Default password: <strong>admin123</strong>. Change ADMIN_PASSWORD in admin/config.php before launch.</small>
</main>
</body>
</html>
