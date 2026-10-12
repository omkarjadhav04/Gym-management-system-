<?php
require_once __DIR__ . '/includes/auth.php';

if (is_admin_logged_in()) {
    header('Location: admin.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (hash_equals(ADMIN_USERNAME, $username) && hash_equals(ADMIN_PASSWORD, $password)) {
        session_regenerate_id(true);
        $_SESSION['admin_logged_in'] = true;
        header('Location: admin.php');
        exit;
    }

    $error = 'Incorrect username or password.';
}
?>

<?php include 'includes/header.php'; ?>

<main class="login-page">
    <section class="login-panel">
        <p class="eyebrow">ADMIN ACCESS</p>
        <h1>Welcome back.</h1>
        <p class="login-copy">Sign in to manage your gym.</p>

        <?php if ($error): ?>
            <div class="alert alert-error" role="alert"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" class="login-form">
            <div class="form-group">
                <label for="username">Username</label>
                <input id="username" name="username" class="form-control" type="text" autocomplete="username" required autofocus>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input id="password" name="password" class="form-control" type="password" autocomplete="current-password" required>
            </div>
            <button type="submit" class="button">Sign in <span aria-hidden="true">&rarr;</span></button>
        </form>
    </section>
</main>

<?php include 'includes/footer.php'; ?>